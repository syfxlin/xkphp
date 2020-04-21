<?php

namespace App\Middleware;

use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class Middleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $this->handle($request, $handler);
    }

    abstract public function handle(
        Request $request,
        MiddlewareRunner $next
    ): ResponseInterface;
}
