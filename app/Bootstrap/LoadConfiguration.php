<?php

namespace App\Bootstrap;

use App\Utils\Config;

class LoadConfiguration extends Bootstrap
{
    public function boot(): void
    {
        $this->app->instance(Config::class, new Config(), 'config');
    }
}
