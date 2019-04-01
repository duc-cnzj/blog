<?php

namespace App\Services;

use App\Contracts\TransformImp;

/**
 * Class RegularRule
 * @package App\Services
 */
class RegularRule implements TransformImp
{
    /**
     * @var TransformImp
     */
    protected $transformer;

    /**
     * RegularRule constructor.
     * @param TransformImp $transformer
     */
    public function __construct(TransformImp $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @return mixed|string|string[]|null
     *
     * @author duc <1025434218@qq.com>
     */
    public function apply()
    {
        $str = $this->getBody();
        $rules = $this->getRules();

        try {
            foreach ($rules as $rule) {
                $str = preg_replace($rule['express'], $rule['replace'], $str);
            }
        } catch (\Exception $e) {
            return $this->getBody();
        }

        return $str;
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getRules()
    {
        if (\Auth::guest()) {
            return [];
        }

        $rules = \Auth::user()->activeArticleRules->pluck(['rule'])->toArray();

        return $rules;
    }

    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function getBody()
    {
        return $this->transformer->getBody();
    }
}
