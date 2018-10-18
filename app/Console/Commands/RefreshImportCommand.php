<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\Events\ModelsImported;
use Illuminate\Contracts\Events\Dispatcher;

class RefreshImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh-scout:import {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重写 scout 导入命令，因为那个只能在命令行模式下启用';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function handle(Dispatcher $events)
    {
        $class = $this->argument('model');

        $model = new $class;

        $events->listen(ModelsImported::class, function ($event) use ($class) {
            $key = $event->models->last()->getScoutKey();

            $this->line('<comment>Imported ['.$class.'] models up to ID:</comment> '.$key);
        });

        $model::makeAllSearchable();

        $events->forget(ModelsImported::class);

        $this->info('All ['.$class.'] records have been imported.');
    }
}
