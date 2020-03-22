<?php

namespace App\Kernel;

use App\Application;

class Request
{
    private $_path;
    private $_get;
    private $_post;
    private $_server;
    private $_files;

    public static function getInstance($request = [])
    {
        return Application::getInstance(self::class, $request);
    }

    public function __construct($request = [])
    {
        $this->_path = $request['path'];
        $this->_get = $request['get'];
        $this->_post = $request['post'];
        $this->_server = $request['server'];
        $this->_files = $request['files'];
    }

    public function make()
    {
        return self::getInstance();
    }

    public function session($name = null, $default = null)
    {
        return session($name, $default);
    }

    public function cookie($name = null, $default = null)
    {
        return cookie($name, $default);
    }

    public function server($name, $default = null)
    {
        if (isset($this->_server[$name])) {
            return $this->_server[$name];
        }
        return $default;
    }

    public function header($name, $default = null)
    {
        $name = str_replace('-', '_', strtoupper($name));
        return $this->server('HTTP_' . $name, $default);
    }

    public function hasHeader($name)
    {
        $name = str_replace('-', '_', strtoupper($name));
        return isset($this->_server["HTTP_$name"]);
    }

    public function getDotData($key, $source)
    {
        $keys = explode('.', $key);
        $data = $source;
        foreach ($keys as $k) {
            if (!isset($data[$k])) {
                return null;
            }
            $data = $data[$k];
        }
        return $data;
    }

    public function all()
    {
        return array_merge($this->_get, $this->_post);
    }

    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $this->_post;
        }
        if (isset($key) && strpos($key, '.') !== false) {
            return $this->getDotData($key, $this->_post);
        }
        if (!isset($this->_post[$key])) {
            return $default;
        }
        return $this->_post[$key];
    }

    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->_get;
        }
        if (!isset($this->_get[$key])) {
            return $default;
        }
        return $this->_get[$key];
    }

    public function has($key)
    {
        if (is_string($key)) {
            return isset($this->_get[$key]) || isset($this->_post[$key]);
        } else {
            foreach ($key as $value) {
                if (!isset($this->_get[$value]) && !isset($this->_post[$value])) {
                    return false;
                }
            }
            return true;
        }
    }

    public function path()
    {
        return $this->_server['PATH_INFO'];
    }

    public function url()
    {
        return $this->_server['REQUEST_URI'];
    }

    public function fullUrl()
    {
        return $this->_server['HTTP_HOST'] . $this->_server['REQUEST_URI'];
    }

    public function method()
    {
        return $this->_server['REQUEST_METHOD'];
    }

    public function isMethod($method)
    {
        return strtoupper($method) === $this->_server['REQUEST_METHOD'];
    }

    public function file($name)
    {
        if (!isset($this->_files[$name])) {
            return null;
        }
        return new RequestFile($this->_files[$name]);
    }

    public function hasFile($name)
    {
        return isset($this->_files[$name]);
    }

    public function pattern($regex)
    {
        return preg_match($regex, $this->path());
    }

    public function ajax()
    {
        return $this->hasHeader('X-Requested-With');
    }

    public function __get($name)
    {
        if (isset($this->_get[$name])) {
            return $this->_get[$name];
        }
        if (isset($this->_post[$name])) {
            return $this->_post[$name];
        }
        if (isset($this->_path[$name])) {
            return $this->_path[$name];
        }
        if (isset($this->_files[$name])) {
            return $this->file($name);
        }
        return null;
    }
}
