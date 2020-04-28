<?php

namespace App\Providers;

use App\Kernel\AspectManager;
use function config;

class AspectProvider extends Provider
{
    /**
     * @var string[]
     */
    protected $aspects;

    public function register(): void
    {
        $this->app->singleton(
            AspectManager::class,
            function () {
                return new AspectManager($this->app);
            },
            'aspect.manager'
        );
        $this->aspects = config('aspect');
        foreach ($this->aspects as $aspect) {
            $this->app->singleton($aspect, null);
        }
    }

    public function boot(): void
    {
        /* @var AspectManager $manager */
        $manager = $this->app->make(AspectManager::class);
        foreach ($this->aspects as $aspect) {
            $manager->putAspect($this->app->make($aspect));
        }
    }
}
