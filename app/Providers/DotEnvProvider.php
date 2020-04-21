<?php

namespace App\Providers;

use Dotenv\Dotenv;

class DotEnvProvider extends Provider
{
    public function register(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }
}
