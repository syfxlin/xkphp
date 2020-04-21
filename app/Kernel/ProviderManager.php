<?php

namespace App\Kernel;

use App\Providers\Provider;
use function array_map;
use function array_walk;
use function config;
use function method_exists;

class ProviderManager
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Provider[]
     */
    protected $providers;

    public function __construct(Container $app)
    {
        $this->app = $app;
        $this->providers = array_map(function (string $item) {
            return new $item($this->app);
        }, config('app.providers'));
    }

    public function register(): void
    {
        array_walk($this->providers, function (Provider $provider) {
            if (method_exists($provider, 'register')) {
                $provider->register();
            }
        });
    }

    public function boot(): void
    {
        array_walk($this->providers, function (Provider $provider) {
            if (method_exists($provider, 'boot')) {
                $this->app->call('boot', [], $provider);
            }
        });
    }
}
