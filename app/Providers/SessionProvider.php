<?php

namespace App\Providers;

use App\Http\SessionManager;

class SessionProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(SessionManager::class, null, 'session');
    }

    public function boot(): void
    {
        $this->app->make(SessionManager::class);
    }
}
