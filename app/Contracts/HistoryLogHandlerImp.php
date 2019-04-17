<?php


namespace App\Contracts;


use Illuminate\Http\Request;

/**
 * Interface HistoryLogHandlerImp
 * @package App\Contracts
 */
interface HistoryLogHandlerImp
{
    /**
     * @param  Request  $request
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function shouldRecord(Request $request): bool ;

    /**
     * @param  HistoryLogHandlerImp  $handler
     * @return HistoryLogHandlerImp
     *
     * @author duc <1025434218@qq.com>
     */
    public function setHandler(HistoryLogHandlerImp $handler): HistoryLogHandlerImp;
}