<?php

namespace App\Middleware;

use App\Facades\App;
use App\Http\Request;
use App\Http\Response;
use App\Http\SessionManager;
use App\Kernel\MiddlewareRunner;
use Psr\Http\Message\ResponseInterface;
use function config;
use function session_name;

class StartSession extends Middleware
{
    public function handle(
        Request $request,
        MiddlewareRunner $next
    ): ResponseInterface {
        $this->start($request);
        return $this->addCookieToResponse($next($request));
    }

    protected function start(Request $request): void
    {
        $config = config('session');
        $id = $request->cookie($config['name'] ?? session_name(), null);
        App::make(SessionManager::class)->start($id, $config);
    }

    /**
     * @param Response $response
     * @return ResponseInterface
     */
    protected function addCookieToResponse(
        ResponseInterface $response
    ): ResponseInterface {
        $cookie = App::make(SessionManager::class)->makeCookie();
        if ($cookie === null) {
            return $response;
        }
        return $response->setCookie($cookie);
    }
}
