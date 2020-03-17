<?php

namespace App\Controllers;

use Exception;
use ReflectionClass;
use ReflectionMethod;

class Controller
{
    private static function makeParam($rel_method, $param)
    {
        $dependencies = $rel_method->getParameters();
        foreach ($param as $key => $value) {
            if (is_numeric($key)) {
                unset($param[$key]);
                $param[$dependencies[$key]->name] = $value;
            }
        }
        $actual_param = [];
        foreach ($dependencies as $dependenci) {
            $class_name = $dependenci->getClass();
            $var_name = $dependenci->getName();
            if (array_key_exists($var_name, $param)) {
                $actual_param[] = $param[$var_name];
            } elseif (is_null($class_name)) {
                if (!$dependenci->isDefaultValueAvailable()) {
                    throw new Exception($var_name . ' 参数没有默认值');
                }
                $actual_param[] = $dependenci->getDefaultValue();
            } else {
                $actual_param[] = self::makeClass($class_name->getName());
            }
        }
        return $actual_param;
    }

    private static function makeClass($class, $param = [])
    {
        $rel_class = new ReflectionClass($class);
        if (!$rel_class->isInstantiable()) {
            throw new Exception($class . ' 类不可实例化');
        }
        $rel_method = $rel_class->getConstructor();
        if (is_null($rel_method)) {
            return new $class();
        }
        return $rel_class->newInstanceArgs(self::makeParam($rel_method, $param));
    }

    private static function runMethod($class, $method, $class_param = [], $method_param = [])
    {
        $object = self::makeClass($class, $class_param);
        $rel_method = new ReflectionMethod($object, $method);
        return $rel_method->invokeArgs($object, self::makeParam($rel_method, $method_param));
    }

    public static function invokeController($class, $method, $class_param = [], $method_param = [])
    {
        return self::runMethod(
            $class,
            $method,
            $class_param,
            $method_param
        );
    }
}
