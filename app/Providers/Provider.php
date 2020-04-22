<?php

namespace App\Providers;

use App\Kernel\Container;

abstract class Provider
{
    /**
     * @var Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    abstract public function register(): void;
}
