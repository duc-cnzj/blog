<?php

namespace App\Services;

/**
 * 系统定义的转化行为
 *
 * Class HandleRule
 * @deprecated
 * @package App\Services
 */
class HandleRule
{
    protected $rules = [
        '/^\^/' => '[↵]*',
    ];

    protected $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }

    public function apply()
    {
        try {
            foreach ($this->rules as $rule => $replace) {
                $this->str = preg_replace($rule, $replace, $this->str);
            }
        } catch (\Exception $e) {
            return $this->str;
        }

        return '/' . $this->str . '/';
    }
}
