<?php

namespace App\Middleware;

use App\Facades\Crypt;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function config;
use function hash_equals;
use function in_array;
use function is_string;
use function response;

class VerifyCsrfToken implements MiddlewareInterface
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
     * @param   ServerRequestInterface  $request  请求对象
     * @param   MiddlewareRunner  $next     事件闭包
     *
     * @return  ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $next
    ): ResponseInterface {
        if (
            $this->isReading($request) ||
            $this->skipVerify($request) ||
            $this->verifyToken($request)
        ) {
            return $this->setToken($request, $next($request));
        }
        return response(
            [
                'status' => 419,
                'message' => 'Request Expired',
                'errors' => ['CSRF Token needs to be updated.']
            ],
            419
        );
    }

    /**
     * 验证 Token 是否有效
     *
     * @param   ServerRequestInterface $request  请求对象
     *
     * @return  bool              Token 是否有效
     */
    protected function verifyToken(ServerRequestInterface $request): bool
    {
        $token = $this->getToken($request);
        $s_token = $request->session()->token();
        return is_string($s_token) &&
            is_string($token) &&
            hash_equals($s_token, $token);
    }

    /**
     * 是否是只读请求
     *
     * @param   ServerRequestInterface $request  请求对象
     *
     * @return  bool              是否是只读请求
     */
    protected function isReading(ServerRequestInterface $request): bool
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * 是否排除 CSRF 验证
     *
     * @param ServerRequestInterface $request 请求对象
     *
     * @return  bool              是否排除 CSRF 验证
     */
    protected function skipVerify(ServerRequestInterface $request): bool
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
     * @param ServerRequestInterface $request 请求对象
     *
     * @return  mixed
     */
    protected function getToken(ServerRequestInterface $request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        if (!$token && ($header = $request->header('X-XSRF-TOKEN'))) {
            $token = Crypt::decrypt($header);
        }
        return $token;
    }

    /**
     * 设置 CSRF Token
     *
     * @param ServerRequestInterface $request 请求对象
     * @param ResponseInterface $response 响应对象
     *
     * @return  ResponseInterface             响应对象
     */
    protected function setToken(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $config = config('session');
        return $response->cookie(
            'XSRF-TOKEN',
            $request->session()->token(),
            60 * $config['cookie_lifetime']
        );
    }
}
