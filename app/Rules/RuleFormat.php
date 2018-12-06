<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class RuleFormat implements Rule
{
    /**
     * 判断验证规则是否通过。
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (isset($value['express'], $value['replace'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取验证错误消息。
     *
     * @return string
     */
    public function message()
    {
        return 'rule 格式不正确！';
    }
}
