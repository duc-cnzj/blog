<?php

namespace App\Services\HistoryLog;

use Illuminate\Http\Request;
use App\Contracts\WhiteListUrlImp;
use App\Contracts\HistoryLogHandlerImp;

/**
 * Class UrlHandler
 * @package App\Services
 */
class UrlHandler implements HistoryLogHandlerImp
{
    /**
     * @var HistoryLogHandlerImp
     */
    protected $nextHandler;

    /**
     * @param  Request  $request
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function shouldRecord(Request $request): bool
    {
        /** @var WhiteListUrlImp $listHandler */
        $listHandler = app(WhiteListUrlImp::class);

        $list = $listHandler->getTreatedListItems();

        if ($request->is(...$list)) {
            return false;
        }

        if ($this->nextHandler) {
            return $this->nextHandler->shouldRecord($request);
        }

        return true;
    }

    /**
     * @param  HistoryLogHandlerImp  $handler
     * @return HistoryLogHandlerImp
     *
     * @author duc <1025434218@qq.com>
     */
    public function setHandler(HistoryLogHandlerImp $handler): HistoryLogHandlerImp
    {
        $this->nextHandler = $handler;

        return $this;
    }
}
