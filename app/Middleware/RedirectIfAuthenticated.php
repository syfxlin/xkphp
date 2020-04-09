<?php

namespace App\Middleware;

use App\Facades\Auth;
use App\Kernel\Request;
use App\Kernel\Response;
use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RedirectIfAuthenticated implements MiddlewareInterface
{
    /**
     * Guest 中间件
     *
     * @param   Request  $request  请求对象
     * @param   Closure  $next     事件闭包
     *
     * @return  Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        if (Auth::check()) {
            return redirect('/home');
        }
        return $next($request);
    }
}
