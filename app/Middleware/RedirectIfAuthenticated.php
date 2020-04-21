<?php

namespace App\Middleware;

use App\Facades\Auth;
use App\Http\Request;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use function redirect;

class RedirectIfAuthenticated extends Middleware
{
    /**
     * Guest 中间件
     *
     * @param Request $request 请求对象
     * @param MiddlewareRunner $next 事件闭包
     *
     * @return ResponseInterface
     */
    public function handle(
        Request $request,
        MiddlewareRunner $next
    ): ResponseInterface {
        if (Auth::check()) {
            return redirect('/home');
        }
        return $next($request);
    }
}
