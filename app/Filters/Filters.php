<?php

namespace App\Filters;

use App\Contracts\Filter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filters
 * @package App\Filters
 */
abstract class Filters implements Filter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected $withoutPrefix = [];

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Filters constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     * @return Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            $method = Str::camel(Str::after($filter, $this->prefix));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this->builder;
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getFilters()
    {
        return array_filter($this->request->only($this->getKeys()), function ($item) {
            return ! is_null($item);
        });
    }

    /**
     * @param string $field
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function getValueBy(string $field)
    {
        $field = Str::snake($field);

        if (in_array($field, $this->withoutPrefix)) {
            return $this->request->input($field);
        }

        return $this->request->input($this->prefix . '_' . $field);
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    private function getKeys(): array
    {
        $prefix = Str::endsWith($this->prefix, '_') ? $this->prefix : $this->prefix . '_';

        return array_merge(array_map(function ($key) use ($prefix) {
            return $prefix . $key;
        }, $this->filters), $this->withoutPrefix);
    }

    /**
     * @param array|mixed $fields
     * @return $this
     *
     * @author duc <1025434218@qq.com>
     */
    public function only($fields)
    {
        $fields = is_array($fields) ? $fields : func_get_args();

        $callback = function ($field) use ($fields) {
            return in_array($field, $fields);
        };

        $this->filters = array_filter($this->filters, $callback);
        $this->withoutPrefix = array_filter($this->withoutPrefix, $callback);

        return $this;
    }
}
