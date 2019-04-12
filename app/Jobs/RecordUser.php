<?php

namespace App\Jobs;

use App\History;

/**
 *
 * Class RecordUser
 * @package App\Jobs
 */
class RecordUser extends Job
{
    /**
     * @var array
     */
    protected $data;

    /**
     * RecordUser constructor.
     * @param  array  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function handle()
    {
        $history = History::query()->create($this->data);

        $address = app('ip')->setIp($history->ip)
            ->getAddress();

        if ($address) {
            $history->update(['address' => $address]);
        }
    }
}
