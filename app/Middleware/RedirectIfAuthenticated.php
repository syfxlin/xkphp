<?php

namespace App\Middleware;

use App\Facades\Auth;
use Closure;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return redirect('/home');
        }
        return $next($request);
    }
}
