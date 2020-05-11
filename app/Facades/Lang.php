<?php

namespace App\Facades;

use App\Utils\Locale;

/**
 * Class Lang
 * @package App\Facades
 *
 * @method static void load(string $locale = null)
 * @method static bool isLoaded()
 * @method static void setLocale(string $locale)
 * @method static string getLocale()
 * @method static string trans(string $key, array $data = [], string $default = null)
 * @method static bool has(string $key)
 *
 * @see \App\Utils\Locale
 */
class Lang extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Locale::class;
    }
}
