<?php

namespace App\Bootstrap;

class BootProviders extends Bootstrap
{
    public function boot(): void
    {
        $this->app->getProviderManager()->boot();
    }
}
