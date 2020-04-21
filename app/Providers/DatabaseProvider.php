<?php

namespace App\Providers;

use App\Database\DB;
use App\Http\Request;

class DatabaseProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(DB::class, null, 'db');
    }

    public function boot(Request $request): void
    {
        $this->app->make(DB::class);
    }
}
