<?php

namespace App;

use App\Bootstrap\BootProviders;
use App\Bootstrap\HandleExceptions;
use App\Bootstrap\LoadConfiguration;
use App\Bootstrap\LoadEnvironmentVariables;
use App\Bootstrap\RegisterFacades;
use App\Bootstrap\RegisterProviders;
use App\Http\Request;
use App\Kernel\ProviderManager;
use App\Kernel\Container;
use App\Kernel\RouteManager;
use function app_path;
use function array_walk;
use function base_path;
use function config_path;
use function env;
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
        // 加载 env 文件
        LoadEnvironmentVariables::class,
        // 加载配置
        LoadConfiguration::class,
        // 注册异常处理
        HandleExceptions::class,
        // 注册门面
        RegisterFacades::class,
        // 注册服务提供者管理器
        RegisterProviders::class,
        // 启动服务
        BootProviders::class
    ];

    /**
     * @var ProviderManager
     */
    public $provider_manager;

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

    /**
     * @codeCoverageIgnore
     */
    protected function bootstrap(): void
    {
        array_walk($this->bootstraps, function ($b) {
            (new $b(self::$app))->boot();
        });
    }

    /**
     * @codeCoverageIgnore
     */
    protected function dispatchToEmit(): void
    {
        // 获取请求
        $request = $this->make(Request::class);

        // 处理
        $response = $this->make(RouteManager::class)->dispatch($request);

        // 发送响应
        $response->send();
    }

    /**
     * 启动 App，程序入口
     *
     * @return Application $app
     *
     * @codeCoverageIgnore
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

        // 路由
        self::$app->dispatchToEmit();
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

    public function environment(string $env = null)
    {
        $app_env = env('APP_ENV', 'production');
        if ($env === null) {
            return $app_env;
        }
        return $app_env === $env;
    }
}
