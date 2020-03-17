<?php

namespace App\Kernel;

use App\Application;

class RequestFile
{
    public $file;
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function isValid()
    {
        return $this->file['error'] === 0;
    }

    public function path()
    {
        return $this->file['tmp_name'];
    }

    public function name()
    {
        return $this->file['name'];
    }

    public function type()
    {
        return $this->file['type'];
    }

    public function store($path)
    {
        $str = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
        $str = str_shuffle($str);
        $this->storeAs($path, substr($str, 0, 10) . "." . pathinfo($this->file['name'], PATHINFO_EXTENSION));
    }

    public function storeAs($path, $filename = null)
    {
        move_uploaded_file(
            $this->file['tmp_name'],
            $path . ($filename !== null ? $filename : $this->file['name'])
        );
    }
}

class Request
{
    private $path_param;
    private $cookie_param;
    private $query_param;
    private $body_param;
    private $server_param;
    private $uploaded_files;

    public static function getInstance($request = [])
    {
        return Application::getInstance(self::class, $request);
    }

    public function __construct($request = [])
    {
        $this->path_param = $request['path_param'];
        $this->cookie_param = $request['cookie_param'];
        $this->query_param = $request['query_param'];
        $this->body_param = $request['body_param'];
        $this->server_param = $request['server_param'];
        $this->uploaded_files = $request['uploaded_files'];
    }

    public function session($name, $default = null)
    {
        return session($name, $default);
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
        return array_merge($this->query_param, $this->body_param);
    }

    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $this->body_param;
        }
        if (isset($key) && strpos($key, '.') !== false) {
            return $this->getDotData($key, $this->body_param);
        }
        if (!isset($this->body_param[$key])) {
            return $default;
        }
        return $this->body_param[$key];
    }

    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query_param;
        }
        if (!isset($this->query_param[$key])) {
            return $default;
        }
        return $this->query_param[$key];
    }

    public function has($key)
    {
        if (is_string($key)) {
            return isset($this->query_param[$key]) || isset($this->body_param[$key]);
        } else {
            foreach ($key as $value) {
                if (!isset($this->query_param[$value]) && !isset($this->body_param[$value])) {
                    return false;
                }
            }
            return true;
        }
    }

    public function path()
    {
        return $this->server_param['PATH_INFO'];
    }

    public function url()
    {
        return $this->server_param['REQUEST_URI'];
    }

    public function fullUrl()
    {
        return $this->server_param['HTTP_HOST'] . $this->server_param['REQUEST_URI'];
    }

    public function method()
    {
        return $this->server_param['REQUEST_METHOD'];
    }

    public function isMethod($method)
    {
        return strtoupper($method) === $this->server_param['REQUEST_METHOD'];
    }

    public function file($name)
    {
        if (!isset($this->uploaded_files[$name])) {
            return null;
        }
        return new RequestFile($this->uploaded_files[$name]);
    }

    public function hasFile($name)
    {
        return isset($this->uploaded_files[$name]);
    }

    public function __get($name)
    {
        if (isset($this->query_param[$name])) {
            return $this->query_param[$name];
        }
        if (isset($this->body_param[$name])) {
            return $this->body_param[$name];
        }
        if (isset($this->path_param[$name])) {
            return $this->path_param[$name];
        }
        return null;
    }
}
