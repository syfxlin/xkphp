<?php

namespace App\Providers;

use App\Facades\App;
use App\Http\Request;
use App\Http\SessionManager;
use function config;
use function session_name;

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
