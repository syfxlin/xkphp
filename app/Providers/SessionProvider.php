<?php

namespace App\Providers;

use App\Facades\App;
use App\Http\Request;
use App\Http\SessionManager;
use function config;
use function session_name;

class SessionProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(
            SessionManager::class,
            function () {
                // Session
                $session_config = config('session');
                $cookies = App::make(Request::class)->getCookieParams();
                $session_id =
                    $cookies[$session_config['name'] ?? session_name()] ?? null;
                if (isset($session_config['id'])) {
                    $session_id = $session_config['id'];
                    unset($session_config['id']);
                }
                return SessionManager::make($session_id, $session_config);
            },
            'session'
        );
    }
}
