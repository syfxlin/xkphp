<?php

namespace App\Kernel;

use App\Application;

class Session
{
    public static function getInstance($sessid = null)
    {
        return Application::getInstance(self::class, $sessid);
    }

    public function __construct($sessid = null)
    {
        $session_config = config('session');
        if ($session_config['save_path']) {
            session_save_path($session_config['save_path']);
        }
        if ($session_config['cookie']) {
            session_name($session_config['cookie']);
        }
        session_set_cookie_params([
            'lifetime' => 60 * ($session_config['life_time'] ?? 1440),
            'path' => $session_config['path'] ?? '/',
            'domain' => $session_config['domain'] ?? null,
            'secure' => $session_config['secure'] ?? false,
            'httponly' => true
        ]);
        if ($sessid !== null) {
            session_id($sessid);
        }
        session_start();
        if (!$this->has('_token')) {
            $this->regenerateToken();
        }
    }

    public function has($name)
    {
        return isset($_SESSION[$name]) && $_SESSION[$name] !== null;
    }

    public function exists($name)
    {
        return isset($_SESSION[$name]);
    }

    public function get($name, $default = null)
    {
        if (!isset($_SESSION[$name])) {
            return $default;
        }
        return $_SESSION[$name];
    }

    public function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function forget($name)
    {
        if (is_string($name)) {
            unset($_SESSION[$name]);
        } else if (is_array($name)) {
            foreach ($name as $key) {
                unset($_SESSION[$key]);
            }
        }
    }

    public function flush()
    {
        session_unset();
    }

    public function regenerate()
    {
        session_regenerate_id();
    }

    public function pull($name, $default = null)
    {
        $value = $this->get($name, $default);
        $this->forget($name);
        return $value;
    }

    public function remove($key)
    {
        $this->forget($key);
    }

    public function token()
    {
        return $this->get('_token');
    }

    public function regenerateToken()
    {
        $this->put('_token', str_random(40));
    }
}
