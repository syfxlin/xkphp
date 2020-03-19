<?php

namespace App\Utils;

class Config
{
    private static $config;

    public function path($config_name)
    {
        return __DIR__ . "/../../config/$config_name.php";
    }

    public function all()
    {
        return self::$config;
    }

    public function get($name, $default = null)
    {
        $names = explode('.', $name);
        $config_name = array_shift($names);
        $config = isset(self::$config[$config_name]) ? self::$config[$config_name] : require $this->path($config_name);
        foreach ($names as $item) {
            if (!isset($config[$item])) {
                return $default;
            }
            $config = $config[$item];
        }
        return $config ?? $default;
    }

    private function setItem($name, $value)
    {
        $names = explode('.', $name);
        $config_name = array_shift($names);
        if (!isset(self::$config[$config_name])) {
            self::$config[$config_name] = require $this->path($config_name);
        }
        $config = &self::$config[$config_name];
        foreach ($names as $item) {
            if (!isset($config[$item])) {
                $config[$item] = [];
            }
            $config = &$config[$item];
        }
        $config = $value;
    }

    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->setItem($key, $value);
            }
        } else {
            $this->setItem($name, $value);
        }
    }

    public function has($name)
    {
        return $this->get($name) !== null;
    }

    public function push($name, $value)
    {
        $this->setItem($name, $value);
    }
}
