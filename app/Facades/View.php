<?php

namespace App\Facades;

/**
 * Class View
 * @package App\Facades
 *
 * @method static bool exists(string $view)
 * @method static \App\Kernel\View assign(array $data)
 * @method static \App\Kernel\View with(string $key, $value)
 * @method static \App\Kernel\View make(string $view, array $data = [])
 * @method static string render()
 *
 * @see \App\Kernel\View
 */
class View extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Kernel\View::class;
    }
}
