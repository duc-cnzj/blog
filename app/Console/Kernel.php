<?php

namespace App\Console;

use App\History;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\RefreshImportCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RefreshImportCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->backupDB($schedule);

        $this->fillNullHistoryAddress($schedule);
    }

    /**
     * @param Schedule $schedule
     *
     * @author duc <1025434218@qq.com>
     */
    protected function backupDB(Schedule $schedule): void
    {
        $enabled = env('BACKUP_ENABLED', false);

        if ($enabled) {
            $schedule->call(function () {
                $olders = Storage::files('backups');
                if (count($olders) > 3) {
                    $deleteFileNames = array_slice($olders, 0, -3);
                    Storage::delete($deleteFileNames); // 删除旧的备份文件
                }
            })
                ->at('02:10')
                ->environments('production');

            // 数据备份
            $schedule->command("db:backup --database=mysql --destination=local --destinationPath=backups/ --timestamp='Y_m_d_H_i_s' --compression=gzip
            ")
                ->at('02:00')
                ->environments('production');

            // 备份数据恢复
            // $lastDumpFile = array_last(Illuminate\Support\Facades\Storage::files('backups'));

            // $schedule->command("db:restore --database=mysql --compression=gzip --source=local --sourcePath=/backups/{$lastDumpFile}
            // ")->everyMinute();
        }
    }

    /**
     * @param Schedule $schedule
     *
     * @author duc <1025434218@qq.com>
     */
    protected function fillNullHistoryAddress(Schedule $schedule): void
    {
        $schedule->call(function () {
            History::query()->whereNull('address')->get()->each(function (History $history) {
                $address = app('ip')->setIp($history->ip)->getAddress();
                if ($address) {
                    $history->update(['address' => $address]);
                }
            });
        })->hourly();
    }
}
