<?php

namespace App\Bootstrap;

use App\Application;

abstract class Bootstrap
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function boot(): void;
}
