<?php

namespace App\Middleware;

use App\Facades\Auth;
use Closure;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        return $next($request);
    }
}
