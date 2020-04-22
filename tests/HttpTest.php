<?php

namespace Test;

class HttpTest extends HttpTestCase
{
    public function testGet(): void
    {
        $response = $this->get('/get');
        $this->assertEquals('true', $response->getContent());
    }
}
