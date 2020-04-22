<?php

namespace Test;

use App\Http\Request;

class ControllerTest extends TestCase
{
    protected static function request(): Request
    {
        return self::buildMockRequest('GET', '/get');
    }

    public function testGet(): void
    {
        $this->assertEquals('true', self::$response->getContent());
    }
}
