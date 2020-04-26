<?php

namespace App\Providers;

use App\Http\CookieManager;

class CookieProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(CookieManager::class, null, 'cookie');
    }
}
