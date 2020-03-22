<?php

namespace App\Middleware;

use App\Facades\Auth;
use App\Kernel\Response;
use Closure;

class Authenticate
{
    /**
     * Auth 中间件
     *
     * @param   Request  $request  请求对象
     * @param   Closure  $next     事件闭包
     *
     * @return  Response
     */
    public function handle($request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        return $next($request);
    }
}
