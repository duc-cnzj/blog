<?php

namespace App\Services;

use App\Contracts\TransformImp;

/**
 * Class ArticleTransformRule
 * @package App\Services
 */
class ArticleTransformRule implements TransformImp
{
    /**
     * @var string
     */
    protected $body;

    /**
     * ArticleTransformRule constructor.
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed|string
     *
     * @author duc <1025434218@qq.com>
     */
    public function apply()
    {
        return $this->getBody();
    }

    /**
     * @return mixed|string
     *
     * @author duc <1025434218@qq.com>
     */
    public function getBody()
    {
        return $this->body;
    }
}
