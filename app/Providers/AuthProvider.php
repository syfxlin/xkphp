<?php

namespace App\Providers;

use App\Kernel\Auth;

class AuthProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(Auth::class, null, 'auth');
    }
}
