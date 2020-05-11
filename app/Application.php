<?php

namespace App;

use App\Bootstrap\BootProviders;
use App\Bootstrap\HandleExceptions;
use App\Bootstrap\LoadConfiguration;
use App\Bootstrap\LoadEnvironmentVariables;
use App\Bootstrap\RegisterFacades;
use App\Bootstrap\RegisterProviders;
use App\Http\Request;
use App\Kernel\Container;
use App\Kernel\ProviderManager;
use App\Kernel\RouteManager;
use function app_path;
use function array_walk;
use function base_path;
use function config;
use function config_path;
use function public_path;
use function resources_path;
use function storage_path;
use function view_path;

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
        // 注册门面
        RegisterFacades::class,
        // 注册异常处理
        HandleExceptions::class,
        // 注册服务提供者管理器
        RegisterProviders::class,
        // 启动服务
        BootProviders::class,
    ];

    /**
     * @var ProviderManager
     */
    protected $provider_manager;

    /**
     * @var callable
     */
    protected $booting_callback;

    /**
     * @var callable
     */
    protected $booted_callback;

    protected function __construct()
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
        $this->instance('path.view', view_path());
        $this->instance('path.resources', resources_path());
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

    public static function create(): Application
    {
        // 若已启动则直接返回
        if (isset(self::$app)) {
            return self::$app;
        }

        // 创建应用
        return self::$app = new self();
    }

    /**
     * 启动 App，程序入口
     *
     * @return Application $app
     *
     * @codeCoverageIgnore
     */
    public function boot(): Application
    {
        // Booting
        if (isset($this->booting_callback)) {
            $booting = $this->booting_callback;
            $booting($this);
        }

        // 初始化
        $this->bootstrap();

        // Booted
        if (isset($this->booted_callback)) {
            $booted = $this->booted_callback;
            $booted($this);
        }

        // 路由
        $this->dispatchToEmit();

        return $this;
    }

    public static function getInstance(): Application
    {
        return self::create();
    }

    public static function setInstance(Application $application): void
    {
        self::$app = $application;
    }

    public function environment(string $env = null)
    {
        $app_env = config('app.env');
        if ($env === null) {
            return $app_env;
        }
        return $app_env === $env;
    }

    public function version(): string
    {
        return config('app.version');
    }

    public function getProviderManager(): ProviderManager
    {
        return $this->provider_manager;
    }

    public function setProviderManager(ProviderManager $manager): void
    {
        $this->provider_manager = $manager;
    }

    public function isBooted(): bool
    {
        return isset(self::$app);
    }

    public function booting(callable $callback): void
    {
        $this->booting_callback = $callback;
    }

    public function booted(callable $callback): void
    {
        $this->booted_callback = $callback;
    }

    public function getLocale(): string
    {
        return config('app.locale');
    }

    public function isLocale(string $locale): bool
    {
        return $this->getLocale() === $locale;
    }
}
