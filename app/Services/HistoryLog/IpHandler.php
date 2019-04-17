<?php

namespace App\Services\HistoryLog;

use Illuminate\Http\Request;
use App\Contracts\WhiteListIpImp;
use App\Contracts\HistoryLogHandlerImp;

/**
 * Class IpHandler
 * @package App\Services
 */
class IpHandler implements HistoryLogHandlerImp
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
        /** @var WhiteListIpImp $handler */
        $handler = app(WhiteListIpImp::class);
        $IpWhiteList = $handler->getTreatedListItems();

        if (! in_array($request->ip(), $IpWhiteList)) {
            if ($this->nextHandler) {
                return $this->nextHandler->shouldRecord($request);
            }

            return false;
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
