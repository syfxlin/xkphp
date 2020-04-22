<?php

namespace App;

use App\Database\DB;
use App\Facades\Annotation;
use App\Facades\App;
use App\Facades\Crypt;
use App\Facades\File;
use App\Facades\Route;
use App\Kernel\ProviderManager;
use App\Kernel\RouteManager;
use App\Providers\Provider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Dotenv\Dotenv;
use App\Kernel\Container;
use App\Http\CookieManager;
use App\Http\Request;
use App\Http\SessionManager;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use function app_path;
use function array_map;
use function class_exists;
use function config;
use function config_path;
use function is_string;
use function session_name;
use function str_replace;
use function strtoupper;
use function substr;

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
     * @var Provider[]
     */
    public $providers = [];

    public function __construct()
    {
        $this->registerBaseBindings();
    }

    public function registerBaseBindings(): void
    {
        self::setInstance($this);
        $this->instance(self::class, $this, 'app');
        $this->instance(Container::class, $this);
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
        self::$app = new self();
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
