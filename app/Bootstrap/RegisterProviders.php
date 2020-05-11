<?php

namespace App\Bootstrap;

use App\Kernel\ProviderManager;
use function config;

class RegisterProviders extends Bootstrap
{
    public function boot(): void
    {
        $this->app->setProviderManager(new ProviderManager($this->app));
        $this->app->getProviderManager()->registers(config('app.providers'));
        $this->app->instance(
            ProviderManager::class,
            $this->app->getProviderManager(),
            'provider_manager'
        );
    }
}
