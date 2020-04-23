<?php

namespace App\Bootstrap;

class BootProviders extends Bootstrap
{
    public function boot(): void
    {
        $this->app->provider_manager->boot();
    }
}
