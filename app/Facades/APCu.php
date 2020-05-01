<?php

namespace App\Facades;

use function apcu_add;
use function apcu_delete;
use function apcu_exists;
use function apcu_fetch;
use function apcu_store;
use function function_exists;

class APCu
{
    public static function isEnable(): bool
    {
        return function_exists('apcu_store') &&
            function_exists('apcu_exists') &&
            function_exists('apcu_fetch') &&
            function_exists('apcu_delete');
    }

    public static function store(string $name, $value): bool
    {
        if (!self::isEnable()) {
            return false;
        }
        return apcu_store($name, $value);
    }

    public static function add(string $name, $value): bool
    {
        if (!self::isEnable()) {
            return false;
        }
        return apcu_add($name, $value);
    }

    public static function delete(string $name): bool
    {
        if (!self::isEnable()) {
            return false;
        }
        return apcu_delete($name);
    }

    public static function exists(string $name): bool
    {
        return self::isEnable() && apcu_exists($name);
    }

    public static function fetch(string $name)
    {
        if (!self::isEnable()) {
            return null;
        }
        return apcu_fetch($name);
    }
}
