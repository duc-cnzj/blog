<?php

namespace App\Services;

use App\Contracts\TransformImp;

class RegularRule implements TransformImp
{
    /**
     * @var TransformImp
     */
    protected $transformer;

    public function __construct(TransformImp $transformer)
    {
        $this->transformer = $transformer;
    }

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

    public function getRules()
    {
        if (\Auth::guest()) {
            return [];
        }

        $rules = \Auth::user()->activeArticleRules->pluck(['rule'])->toArray();

        return $rules;
    }

    public function getBody()
    {
        return $this->transformer->getBody();
    }
}
