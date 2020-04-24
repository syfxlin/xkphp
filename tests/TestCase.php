<?php

namespace Test;

use App\Application;
use App\Bootstrap\BootProviders;
use App\Bootstrap\RegisterProviders;
use App\Facades\App;
use App\Facades\Crypt;
use App\Http\Request;
use App\Http\Response;
use App\Http\Stream;
use App\Kernel\RouteManager;
use App\Providers\RequestProvider;
use App\Providers\RouteProvider;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use RuntimeException;
use function array_filter;
use function array_map;
use function array_merge;
use function array_walk;
use function config;
use function in_array;
use function strtoupper;

abstract class TestCase extends BaseTestCase
{
    public const ACCEPT_VIEW = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8';
    public const ACCEPT_JSON = 'application/json';
    public const ACCEPT_RAW = 'text/plain';

    /**
     * @var bool
     */
    protected static $booted = false;

    /**
     * @var Application
     */
    protected static $app;

    /**
     * @var Request
     */
    protected static $request;

    public static function setUpBeforeClass(): void
    {
        if (self::$booted) {
            return;
        }
        // 导入依赖
        require_once __DIR__ . '/../vendor/autoload.php';
        define('BASE_PATH', dirname(__DIR__) . '/');

        self::boot();
        self::$booted = true;
    }

    protected static function boot(): void
    {
        // 启动
        self::$app = new Application();

        // 初始化
        $ref = new ReflectionClass(Application::class);
        $prop = $ref->getProperty('bootstraps');
        $prop->setAccessible(true);
        $bootstraps = $prop->getValue(self::$app);
        array_walk($bootstraps, function ($b) {
            if (
                in_array(
                    $b,
                    [RegisterProviders::class, BootProviders::class],
                    true
                )
            ) {
                return;
            }
            (new $b(self::$app))->boot();
        });

        // 移除需要另外配置的服务提供者
        $providers = array_filter(config('app.providers'), function ($item) {
            return $item !== RequestProvider::class;
        });
        config(['app.providers' => $providers]);

        // 注册启动服务提供者
        (new RegisterProviders(self::$app))->boot();
        (new BootProviders(self::$app))->boot();

        // 注册请求
        self::registerRequest();
    }

    protected static function registerRequest(): void
    {
        App::bind(
            Request::class,
            function () {
                $request = self::$request;
                // Decrypt Cookies
                $request_cookies = $request->getCookieParams();
                $request_cookies = array_map(function ($cookie) {
                    try {
                        return Crypt::decrypt($cookie);
                    } catch (RuntimeException $e) {
                        return $cookie;
                    }
                }, $request_cookies);
                $request = $request->withCookieParams($request_cookies);
                return $request;
            },
            false,
            'request'
        );
    }

    protected function buildMockRequest(
        string $method,
        string $uri,
        array $parameters = [],
        string $accept = self::ACCEPT_JSON,
        array $headers = [],
        array $cookies = [],
        array $files = [],
        string $raw_body = '',
        string $protocol = '1.1'
    ): Request {
        $method = strtoupper($method);

        $url_arr = parse_url($uri);
        $url_params = [];
        if (isset($url_arr['query'])) {
            parse_str($url_arr['query'], $url_params);
        }

        $defaultHeaders = [
            'host' => '127.0.0.1',
            'connection' => 'keep-alive',
            'cache-control' => 'max-age=0',
            'user-agent' => 'PHPUnit',
            'upgrade-insecure-requests' => '1',
            'accept' => $accept,
            'dnt' => '1',
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'zh-CN,zh;q=0.8,en;q=0.6,it-IT;q=0.4,it;q=0.2'
        ];

        $headers = array_merge($headers, $defaultHeaders);

        $server = [
            'request_method' => $method,
            'request_uri' => $uri,
            'path_info' => '/',
            'request_time' => microtime(),
            'request_time_float' => microtime(true),
            'server_port' => 80,
            'remote_port' => 49999,
            'remote_addr' => '127.0.0.1',
            'master_time' => microtime(),
            'server_protocol' => 'HTTP/1.1'
        ];

        $get = [];
        $post = [];

        if ($method === 'GET') {
            $get = $parameters;
        } elseif ($method === 'POST') {
            $post = $parameters;
        }

        if (!empty($url_params)) {
            $get = array_merge($url_params, $get);
        }

        return new Request(
            $server,
            $files,
            $uri,
            $method,
            Stream::make($raw_body, 'php://temp', 'wb+'),
            $headers,
            $cookies,
            $get,
            $post,
            $protocol
        );
    }

    protected function handleRequest(Request $request = null): Response
    {
        self::$request = $request ?? $this->buildMockRequest('GET', '/');
        return self::$app->make(RouteManager::class)->dispatch(self::$request);
    }

    protected function request(
        string $method,
        string $uri,
        array $parameters = [],
        string $accept = self::ACCEPT_JSON,
        array $headers = [],
        array $cookies = [],
        array $files = [],
        string $raw_body = '',
        string $protocol = '1.1'
    ): Response {
        $request = $this->buildMockRequest(
            $method,
            $uri,
            $parameters,
            $accept,
            $headers,
            $cookies,
            $files,
            $raw_body,
            $protocol
        );
        return $this->handleRequest($request);
    }
}
