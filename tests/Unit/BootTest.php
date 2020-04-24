<?php

namespace Test\Unit;

use Test\TestCase;
use function define;
use function dirname;

class BootTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        return;
    }

    public function testBoot(): void
    {
        if (static::$booted) {
            return;
        }
        // 导入依赖
        require_once __DIR__ . '/../../vendor/autoload.php';
        define('BASE_PATH', dirname(__DIR__, 2) . '/');

        static::boot();
        static::$booted = true;

        static::$app::getInstance();

        $response = $this->request('GET', '/get');
        $this->assertEquals('true', $response->getContent());
    }
}
