<?php

namespace App\Middleware;

use App\Facades\Auth;
use App\Kernel\Request;
use App\Kernel\Response;
use Closure;

class RedirectIfAuthenticated
{
    /**
     * Guest 中间件
     *
     * @param   Request  $request  请求对象
     * @param   Closure  $next     事件闭包
     *
     * @return  Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return redirect('/home');
        }
        return $next($request);
    }
}
