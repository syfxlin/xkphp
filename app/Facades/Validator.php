<?php

namespace App\Facades;

use Inhere\Validate\FieldValidation;

class Validator
{
    public static function make(array $data, array $rules = [], array $translates = [], string $scene = '', bool $startValidate = false)
    {
        $f_rules = [];
        foreach ($rules as $name => $rule) {
            if (is_string($rule)) {
                $f_rules[] = [$name, $rule];
            } else {
                $f_rules[] = array_merge([$name], $rule);
            }
        }
        return FieldValidation::make($data, $f_rules, $translates, $scene, $startValidate);
    }

    public static function check(array $data, array $rules = [], array $translates = [], string $scene = '')
    {
        return self::make($data, $rules, $translates, $scene, true);
    }

    /**
     * 将验证器的错误转换成视图显示的错误
     *
     * @param   array  $errors  验证器错误列表
     *
     * @return  array           视图显示的错误列表
     */
    public static function convertViewErrors(array $errors): array
    {
        $result = [];
        foreach ($errors as $error) {
            $result[$error['name']] = $error['msg'];
        }
        return $result;
    }
}
