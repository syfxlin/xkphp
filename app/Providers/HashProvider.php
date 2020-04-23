<?php

namespace App\Providers;

use App\Utils\Hash;

class HashProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(Hash::class, null, 'hash');
    }
}
