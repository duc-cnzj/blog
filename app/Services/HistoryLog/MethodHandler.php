<?php

namespace App\Services\HistoryLog;

use Illuminate\Http\Request;
use App\Contracts\HistoryLogHandlerImp;

/**
 * Class MethodHandler
 * @package App\Services\HistoryLog
 */
class MethodHandler implements HistoryLogHandlerImp
{
    /**
     * @var array
     */
    protected $dontRecordMethodNames = ['OPTIONS', 'HEAD'];

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
        if (! in_array($request->method(), $this->dontRecordMethodNames)) {
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
