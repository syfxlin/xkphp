<?php

namespace Test;

use App\Application;
use App\Kernel\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;

class TestCase extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        // 导入依赖
        require_once __DIR__ . '/../vendor/autoload.php';
        define('BASE_PATH', dirname(__DIR__) . '/');

        // 启动
        Application::$app = new Container();
        $ref = new ReflectionClass(Application::class);
        $method = $ref->getMethod('bootDotenv');
        $method->setAccessible(true);
        $method->invoke(null);
        $method = $ref->getMethod('bootRequest');
        $method->setAccessible(true);
        $method->invoke(null);
    }
}
