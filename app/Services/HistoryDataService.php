<?php

namespace App\Services;

use App\History;
use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Class HistoryDataService
 * @package App\Services
 */
class HistoryDataService
{
    /**
     * @param  string  $unit
     * @param  int  $section
     * @param  int  $subWeek
     * @param  string  $from
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getData(string $unit, int $section, int $subWeek, string $from): array
    {
        $times = $this->getPeriodSections($unit, $section, $subWeek);

        list($totalVisits, $result, $formatTimes) = $this->getDetailTotalVisitsAndTimes($unit, $times, $from);

        return [
            'times'        => $formatTimes,
            'total'        => collect($totalVisits)->sum->count,
            'detail'       => $result,
            'total_visits' => $totalVisits,
        ];
    }

    /**
     * @param  string  $unit
     * @param  int  $periodTimes
     * @param  int  $subWeek
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    private function getPeriodSections(string $unit, int $periodTimes, int $subWeek): array
    {
        $times = [];
        switch ($unit) {
            case 'day':
                $perHour = intval(floor(24 / ($periodTimes ?? 6)));
                $startOfDay = Carbon::now()->startOfDay();
                while ($periodTimes > 0) {
                    if ($periodTimes == 1) {
                        $times[] = [$startOfDay, (clone $startOfDay)->endOfDay()];
                    } else {
                        $times[] = [$startOfDay, $startOfDay = (clone $startOfDay)->addHours($perHour)];
                    }
                    $periodTimes--;
                }
                break;
            case 'week':
                $days = 7;
                $startOfDay = Carbon::now()->startOfWeek()->subWeeks($subWeek);
                while ($days > 0) {
                    $times[] = [$startOfDay, $startOfDay = (clone $startOfDay)->addDay()];
                    $days--;
                }
                break;
            default:
                throw new \RuntimeException('invalid unit.');
        }

        return $times;
    }

    /**
     * @param  string  $unit
     * @param  array  $times
     * @param  string  $from
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    private function getDetailTotalVisitsAndTimes(string $unit, array $times, string $from): array
    {
        $result = [];
        $totalVisits = [];
        $formatTimes = [];

        $allHistories = History::onlySee($from)
            ->whereBetween('visited_at', [Arr::first($times)[0], Arr::last($times)[1]])
            ->removeWhiteListIps()
            ->get(['id', 'ip', 'address', 'url', 'visited_at']);

        /** @var Carbon[] $period */
        foreach ($times as $period) {
            $histories = $allHistories->whereBetween('visited_at', $period);

            $data = $histories->reduce(function ($carry, $item) {
                $tmpData[$item->ip] = [
                    'ip'      => $item->ip,
                    'count'   => ($carry[$item->ip]['count'] ?? 0) + 1,
                    'address' => $carry[$item->ip]['address'] ?? $item->address,
                    'url'     => $this->getUrlData($carry, $item),
                ];

                $tmpData = array_merge($carry, $tmpData);

                return $tmpData;
            }, []);

            if (! empty($data)) {
                $totalVisits[] = $data;
                $sortData = collect($data)->sortByDesc('count')->values()->toArray();
            } else {
                $sortData = [];
            }

            $result[] = [
                'count' => $histories->count(),
                'time'  => $formatTimes[] = $this->getDateFormat($unit, $period),
                'data'  => $sortData,
            ];
        }

        $totalVisits = collect($totalVisits)
            ->flatten(1)
            ->sortByDesc('count')
            ->values();

        return [$totalVisits, $result, $formatTimes];
    }

    /**
     * @param array $carry
     * @param History $item
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    private function getUrlData($carry, History $item): array
    {
        if (! isset($carry[$item->ip]['url'])) {
            return [$item->url => 1];
        }

        if (! isset($carry[$item->ip]['url'][$item->url])) {
            return array_merge($carry[$item->ip]['url'], [$item->url => 1]);
        }

        $carry[$item->ip]['url'][$item->url] += 1;

        return $carry[$item->ip]['url'];
    }

    /**
     * @param  string  $unit
     * @param  array  $period
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    private function getDateFormat(string $unit, array $period): string
    {
        switch ($unit) {
            case 'day':
                $result = $period[0]->format($this->getFormat($unit)) . ' - ' . $period[1]->format($this->getFormat($unit));
                break;
            case 'week':
                $result = $period[0]->format($this->getFormat($unit));
                break;
            default:
                $result = $period[0]->format($this->getFormat($unit)) . ' - ' . $period[1]->format($this->getFormat($unit));
        }

        return $result;
    }

    /**
     * @param  string  $unit
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    private function getFormat(string $unit): string
    {
        switch ($unit) {
            case 'day':
                $format = 'H:i';
                break;
            case 'week':
                $format = 'Y-m-d';
                break;
            default:
                $format = 'Y-m-d H:i:s';
        }

        return $format;
    }
}
