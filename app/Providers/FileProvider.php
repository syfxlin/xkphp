<?php

namespace App\Providers;

use App\Utils\File;

class FileProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(File::class, null, 'file');
    }
}
