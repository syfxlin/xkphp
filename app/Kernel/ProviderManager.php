<?php

namespace App\Kernel;

use App\Application;
use App\Providers\Provider;
use function array_map;
use function array_walk;
use function is_string;
use function method_exists;

class ProviderManager
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Provider[]
     */
    protected $providers;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string|Provider $provider
     * @return Provider|null
     */
    public function getProvider($provider): ?Provider
    {
        if (is_string($provider)) {
            return $this->providers[$provider] ?? null;
        }
        return $provider;
    }

    public function setProvider($name, $provider): void
    {
        $this->providers[$name] = $provider;
    }

    public function register($provider, $force = false): Provider
    {
        if (!$force && ($reg = $this->getProvider($provider)) !== null) {
            return $reg;
        }
        if (is_string($provider)) {
            $name = $provider;
            $provider = new $provider($this->app);
            $this->setProvider($name, $provider);
        }
        if (method_exists($provider, 'register')) {
            $provider->register();
        }
        return $provider;
    }

    public function registers(array $providers): array
    {
        return array_map(function ($provider) {
            return $this->register($provider);
        }, $providers);
    }

    public function boot(): void
    {
        array_walk($this->providers, function (Provider $provider) {
            if (!$provider->booted && method_exists($provider, 'boot')) {
                $this->app->call('boot', [], $provider);
                $provider->booted = true;
            }
        });
    }
}
