<?php

namespace App\Middleware;

use App\Facades\Auth;
use App\Kernel\MiddlewareRunner;
use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function redirect;

class RedirectIfAuthenticated implements MiddlewareInterface
{
    /**
     * Guest 中间件
     *
     * @param   ServerRequestInterface  $request  请求对象
     * @param   MiddlewareRunner  $next     事件闭包
     *
     * @return  ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $next
    ): ResponseInterface {
        if (Auth::check()) {
            return redirect('/home');
        }
        return $next($request);
    }
}
