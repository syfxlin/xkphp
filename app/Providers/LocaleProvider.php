<?php

namespace App\Providers;

use App\Utils\Locale;

class LocaleProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            Locale::class,
            function () {
                return new Locale($this->app->getLocale());
            },
            'lang'
        );
    }

    public function boot(): void
    {
        $this->app->make(Locale::class)->load();
    }
}
