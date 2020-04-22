<?php

namespace App;

use App\Bootstrap\LoadConfiguration;
use App\Bootstrap\LoadEnvironmentVariables;
use App\Kernel\ProviderManager;
use App\Kernel\Container;
use function app_path;
use function array_walk;
use function base_path;
use function config;
use function config_path;
use function public_path;
use function realpath;
use function storage_path;

/**
 * Class Application
 * @package App
 */
class Application extends Container
{
    /**
     * 存储 App 中所有的单例 instance
     *
     * @var Application
     */
    public static $app;

    /**
     * @var string[]
     */
    protected $bootstraps = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class
    ];

    public function __construct()
    {
        // 基础绑定
        $this->registerBaseBindings();

        // 绑定路径
        $this->registerPath();
    }

    protected function registerBaseBindings(): void
    {
        self::setInstance($this);
        $this->instance(self::class, $this, 'app');
        $this->instance(Container::class, $this);
    }

    protected function registerPath(): void
    {
        $this->instance('path', base_path());
        $this->instance('path.app', app_path());
        $this->instance('path.config', config_path());
        $this->instance('path.public', public_path());
        $this->instance('path.storage', storage_path());
        $this->instance('path.view', realpath(BASE_PATH . '/app/Views'));
    }

    protected function bootstrap(): void
    {
        array_walk($this->bootstraps, function ($b) {
            (new $b(self::$app))->boot();
        });
    }

    protected function bootProvider(): void
    {
        $provider = new ProviderManager(self::$app);
        $provider->registers(config('app.providers'));
    }

    /**
     * 启动 App，程序入口
     *
     * @return  Container  $app
     */
    public static function boot(): Application
    {
        // 若已启动则直接返回
        if (isset(self::$app)) {
            return self::$app;
        }

        // 创建应用
        self::$app = new self();

        // 初始化
        self::$app->bootstrap();

        // 注册服务提供者
        self::$app->bootProvider();
        return self::$app;
    }

    public static function getInstance(): Application
    {
        return self::boot();
    }

    public static function setInstance(Application $application): void
    {
        self::$app = $application;
    }
}
