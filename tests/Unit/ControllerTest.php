<?php

namespace Test\Unit;

use Test\TestCase;

class ControllerTest extends TestCase
{
    public function testGet(): void
    {
        $response = $this->request('GET', '/get');
        $this->assertEquals('true', $response->getContent());
    }

    public function testGetRoot(): void
    {
        $response = $this->request('GET', '/');
        $this->assertNotEmpty($response->getContent());
    }

    public function testGetUsers(): void
    {
        $response = $this->request('GET', '/users');
        $this->assertEquals('false', $response->getContent());
    }
}
