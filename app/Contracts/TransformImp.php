<?php

namespace App\Contracts;

/**
 * Interface TransformImp
 * @package App\Contracts
 */
interface TransformImp
{
    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function apply();

    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function getBody();
}
