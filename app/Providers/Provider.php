<?php

namespace App\Providers;

use App\Application;

abstract class Provider
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var bool
     */
    public $booted = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function register(): void;
}
