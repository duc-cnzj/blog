<?php

namespace App\Http\Controllers\Admin;

use App\History;
use Illuminate\Http\Request;
use App\Filters\HistoryFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryResource;

/**
 * Class HistoryController
 * @package App\Http\Controllers\Admin
 */
class HistoryController extends Controller
{
    /**
     * @param  HistoryFilter  $filter
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(HistoryFilter $filter, Request $request)
    {
        $histories = History::with('userable:id,name')
            ->removeWhiteListIps()
            ->filter($filter)
            ->orderByDesc('visited_at')
            ->paginate(
                $request->input('page_size') ?? 10,
                [
                    'id',
                    'ip',
                    'url',
                    'method',
                    'content',
                    'address',
                    'user_agent',
                    'visited_at',
                    'status_code',
                    'userable_id',
                    'userable_type',
                ]
            );

        return HistoryResource::collection($histories);
    }

    /**
     * @param  int  $id
     * @return HistoryResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function show(int $id)
    {
        return new HistoryResource(History::with('userable')->findOrFail($id));
    }
}
