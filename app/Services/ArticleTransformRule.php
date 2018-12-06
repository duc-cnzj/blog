<?php


namespace App\Services;


use App\Contracts\TransformImp;

class ArticleTransformRule implements TransformImp
{
    protected $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function apply()
    {
        return $this->getBody();
    }

    public function getBody()
    {
        return $this->body;
    }
}