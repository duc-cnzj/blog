<?php

namespace App\Services;

/**
 * 系统定义的转化行为
 *
 * Class HandleRule
 * @package App\Services
 */
class HandleRule
{
    /**
     * @var array
     */
    protected $rules = [
        '/^\^/' => '[↵]*',
    ];

    /**
     * @var string
     */
    protected $str;

    /**
     * HandleRule constructor.
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->str = $str;
    }

    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
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
