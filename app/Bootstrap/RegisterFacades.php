<?php

namespace App\Bootstrap;

use App\Facades\Facade;

class RegisterFacades extends Bootstrap
{
    public function boot(): void
    {
        Facade::setApplication($this->app);
    }
}
