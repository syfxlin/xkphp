<?php

namespace Test;

use App\Http\Request;

class ControllerTest extends TestCase
{
    public function testGet(): void
    {
        $response = $this->request('GET', '/get');
        $this->assertEquals('true', $response->getContent());
    }

    public function testGet2(): void
    {
        $response = $this->request('GET', '/get');
        $this->assertEquals('true', $response->getContent());
    }
}
