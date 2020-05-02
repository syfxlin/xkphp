<?php

namespace App\Providers;

use App\Application;

abstract class Provider implements \App\Contracts\Provider
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
}
