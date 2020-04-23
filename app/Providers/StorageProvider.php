<?php

namespace App\Providers;

use App\Utils\Storage;

class StorageProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(Storage::class, null, 'storage');
    }
}
