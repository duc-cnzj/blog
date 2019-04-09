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
     * @var
     */
    protected $data;

    /**
     * RecordUser constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function handle()
    {
        History::query()->create($this->data);
    }
}
