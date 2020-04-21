<?php

namespace App\Providers;

use App\Kernel\RouteManager;

class RouteProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(RouteManager::class, null, 'route');
    }

    public function boot(): void
    {
        $this->app->make(RouteManager::class);
    }
}
