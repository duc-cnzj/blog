<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckEmptyArray implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $arr = array_filter($value);

        return ! empty($arr);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return '提交数据不能为空';
    }
}
