<?php

namespace App\Middleware;

use App\Facades\Crypt;
use App\Kernel\Request;
use App\Kernel\Response;
use Closure;

class VerifyCsrfToken
{
    /**
     * 排除使用 CSRF 的 URL（正则）
     *
     * @var array
     */
    protected $except = [];

    /**
     * 中间件事件
     *
     * @param   Request  $request  请求对象
     * @param   Closure  $next     事件闭包
     *
     * @return  Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $this->isReading($request) ||
            $this->skipVerify($request) ||
            $this->verifyToken($request)
        ) {
            return $this->setToken($request, $next($request));
        }
        return response([
            'status' => 419,
            'message' => 'Request Expired',
            'errors' => [
                'CSRF Token needs to be updated.'
            ]
        ], 419);
    }

    /**
     * 验证 Token 是否有效
     *
     * @param   Request $request  请求对象
     *
     * @return  bool              Token 是否有效
     */
    protected function verifyToken(Request $request): bool
    {
        $token = $this->getToken($request);
        $s_token = $request->session()->token();
        return is_string($s_token) &&
            is_string($token) && hash_equals($s_token, $token);
    }

    /**
     * 是否是只读请求
     *
     * @param   Request $request  请求对象
     *
     * @return  bool              是否是只读请求
     */
    protected function isReading(Request $request): bool
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * 是否排除 CSRF 验证
     *
     * @param   Request $request  请求对象
     *
     * @return  bool              是否排除 CSRF 验证
     */
    protected function skipVerify(Request $request): bool
    {
        foreach ($this->except as $except) {
            if ($request->pattern($except)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取 CSRF Token
     *
     * @param   Request $request  请求对象
     *
     * @return  mixed
     */
    protected function getToken(Request $request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = Crypt::decrypt($header);
        }
        return $token;
    }

    /**
     * 设置 CSRF Token
     *
     * @param   Request   $request   请求对象
     * @param   Response  $response  响应对象
     *
     * @return  Response             响应对象
     */
    protected function setToken(Request $request, Response $response): Response
    {
        $config = config('session');
        $response->cookie('XSRF-TOKEN', $request->session()->token(), time() + 60 * $config['life_time']);
        return $response;
    }
}
