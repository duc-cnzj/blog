<?php

namespace App\Services\HistoryLog;

use Illuminate\Http\Request;
use App\Contracts\HistoryLogHandlerImp;

/**
 * Class HistoryLogHandler
 * @package App\Services\HistoryLog
 */
class HistoryLogHandler implements HistoryLogHandlerImp
{
    /**
     * @var array
     */
    protected $handlers = [
        IpHandler::class,
        UrlHandler::class,
        MethodHandler::class,
    ];

    /**
     * @param  Request  $request
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function shouldRecord(Request $request): bool
    {
        /** @var HistoryLogHandlerImp $lastHandler */
        $lastHandler = array_reduce($this->handlers, function ($prevHandler, $handler) {
            /** @var HistoryLogHandlerImp $handlerObj */
            $handlerObj = new $handler;

            if ($prevHandler) {
                $handlerObj->setHandler($prevHandler);
            }

            return $handlerObj;
        }, null);

        return $lastHandler->shouldRecord($request);
    }

    /**
     * @param  HistoryLogHandlerImp  $handler
     * @return HistoryLogHandlerImp
     *
     * @author duc <1025434218@qq.com>
     */
    public function setHandler(HistoryLogHandlerImp $handler): HistoryLogHandlerImp
    {
        $this->handlers[] = $handler;

        return $this;
    }
}
