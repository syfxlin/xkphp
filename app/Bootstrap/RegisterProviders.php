<?php

namespace App\Bootstrap;

use App\Kernel\ProviderManager;
use function config;

class RegisterProviders extends Bootstrap
{
    public function boot(): void
    {
        $this->app->provider_manager = new ProviderManager($this->app);
        $this->app->provider_manager->registers(config('app.providers'));
        $this->app->instance(
            ProviderManager::class,
            $this->app->provider_manager,
            'provider_manager'
        );
    }
}
