<?php

namespace App\Providers;

use App\Database\DB;

class DatabaseProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(DB::class, null, 'db');
    }

    public function boot(): void
    {
        $this->app->make(DB::class);
    }
}
