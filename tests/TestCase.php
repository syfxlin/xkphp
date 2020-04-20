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
        self::invokePrivateMethod($ref, 'bootDotenv');
        self::invokePrivateMethod($ref, 'bootRequest');
        self::invokePrivateMethod($ref, 'bootAnnotation');
        self::invokePrivateMethod($ref, 'parseAnnotation');
        self::invokePrivateMethod($ref, 'bootDatabase');
    }

    private static function invokePrivateMethod(
        ReflectionClass $class,
        string $method_name,
        bool $isStatic = true
    ) {
        $method = $class->getMethod($method_name);
        $method->setAccessible(true);
        return $method->invoke($isStatic ? null : $class);
    }
}
