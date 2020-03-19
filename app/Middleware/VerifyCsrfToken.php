<?php

namespace App\Middleware;

use App\Facades\Crypt;
use Closure;

class VerifyCsrfToken
{
    protected $except = [];

    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->skipVerify($request) ||
            $this->verifyToken($request)
        ) {
            return $this->setToken($request, $next($request));
        }
        return response('419', 419);
    }

    protected function verifyToken($request)
    {
        $token = $this->getToken($request);
        $s_token = $request->session()->token();
        return is_string($s_token) &&
            is_string($token) && hash_equals($s_token, $token);
    }

    protected function isReading($request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    protected function skipVerify($request)
    {
        foreach ($this->except as $except) {
            if ($request->pattern($except)) {
                return true;
            }
        }
        return false;
    }

    protected function getToken($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = Crypt::decrypt($header);
        }
        return $token;
    }

    protected function setToken($request, $response)
    {
        $config = config('session');
        $response->cookie('XSRF-TOKEN', $request->session()->token(), time() + 60 * $config['life_time']);
        return $response;
    }
}
