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
        $session_config = require_once __DIR__ . "/../../config/session.php";
        if ($session_config['save_path']) {
            session_save_path($session_config['save_path']);
        }
        if ($session_config['life_time']) {
            session_set_cookie_params($session_config['life_time']);
        }
        if ($sessid !== null) {
            session_id($sessid);
        }
        session_start();
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
}
