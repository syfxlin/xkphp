<?php

namespace App\Utils;

use App\Exceptions\Utils\LocaleNotExistException;
use function data_get_dot;
use function file_exists;
use function resources_path;
use function sprintf_array;

class Locale
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var mixed
     */
    protected $locale_data;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function load(string $locale = null): void
    {
        $this->locale = $locale ?? $this->locale;
        $lang_path = resources_path("lang/$this->locale.php");
        if (!file_exists($lang_path)) {
            throw new LocaleNotExistException(
                "[$this->locale] language file not found"
            );
        }
        $this->locale_data = require $lang_path;
    }

    public function isLoaded(): bool
    {
        return isset($this->locale_data);
    }

    public function setLocale(string $locale): void
    {
        $this->locale_data = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function trans(
        string $key,
        array $data = [],
        string $default = null
    ): string {
        $string = data_get_dot($key, $this->locale_data, $default);
        return sprintf_array($string, $data);
    }

    public function has(string $key): bool
    {
        return data_get_dot($key, $this->locale_data) !== null;
    }
}
