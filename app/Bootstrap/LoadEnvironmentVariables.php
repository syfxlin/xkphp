<?php

namespace App\Bootstrap;

use Dotenv\Dotenv;

class LoadEnvironmentVariables extends Bootstrap
{
    public function boot(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }
}
