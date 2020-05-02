<?php

namespace App\Contracts;

use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

interface Middleware extends MiddlewareInterface
{
    public function handle(
        Request $request,
        MiddlewareRunner $next
    ): ResponseInterface;
}
