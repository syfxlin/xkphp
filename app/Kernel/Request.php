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

    public static function getInstance($request = []): Request
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

    /**
     * 获取 Request 实例
     *
     * @return  Request
     */
    public function make(): Request
    {
        return self::getInstance();
    }

    /**
     * 获取 Session 实例或者值
     *
     * @param   string|null  $name     Session 名称
     * @param   string|null  $default  默认值
     *
     * @return  Session
     */
    public function session($name = null, $default = null): Session
    {
        return session($name, $default);
    }

    /**
     * 获取 Cookie 实例或者值
     *
     * @param   string|null  $name     Cookie 名称
     * @param   string|null  $default  默认值
     *
     * @return  Cookie
     */
    public function cookie($name = null, $default = null): Cookie
    {
        return cookie($name, $default);
    }

    /**
     * 获取 SERVER 参数
     *
     * @param   string       $name     要获取的 SERVER 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
    public function server(string $name, $default = null)
    {
        if (isset($this->_server[$name])) {
            return $this->_server[$name];
        }
        return $default;
    }

    /**
     * 获取 Header 的值
     *
     * @param   string       $name     要获取的 Header 的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
    public function header(string $name, $default = null)
    {
        $name = str_replace('-', '_', strtoupper($name));
        return $this->server('HTTP_' . $name, $default);
    }

    /**
     * 判断 Header 是否存在
     *
     * @param   string  $name  Header 的名称
     *
     * @return  bool
     */
    public function hasHeader(string $name): bool
    {
        $name = str_replace('-', '_', strtoupper($name));
        return isset($this->_server["HTTP_$name"]);
    }

    protected function getDotData(string $key, $source)
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

    /**
     * 获取 GET 和 POST 的参数
     *
     * @return  array
     */
    public function all(): array
    {
        return array_merge($this->_get, $this->_post);
    }

    /**
     * 获取 POST 参数
     *
     * @param   string       $name     要获取的 POST 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
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

    /**
     * 获取 GET 参数
     *
     * @param   string       $name     要获取的 GET 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
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

    /**
     * 判断参数是否存在
     *
     * @param   string  $key  参数名称
     *
     * @return  bool
     */
    public function has($key): bool
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

    /**
     * 获取当前的 PATH
     *
     * @return  string
     */
    public function path(): string
    {
        return $this->_server['PATH_INFO'];
    }

    /**
     * 获取当前请求的URL
     *
     * @return  string
     */
    public function url(): string
    {
        return $this->_server['REQUEST_URI'];
    }

    /**
     * 获取完整的请求 URL
     *
     * @return  string
     */
    public function fullUrl(): string
    {
        return $this->_server['HTTP_HOST'] . $this->_server['REQUEST_URI'];
    }

    /**
     * 获取当前请求的方法
     *
     * @return  string
     */
    public function method(): string
    {
        return $this->_server['REQUEST_METHOD'];
    }

    /**
     * 判断当前请求的方法
     *
     * @param   string  $method  请求的方法
     *
     * @return  bool
     */
    public function isMethod(string $method): bool
    {
        return strtoupper($method) === $this->_server['REQUEST_METHOD'];
    }

    /**
     * 获取上传的文件
     *
     * @param   string  $name  上传文件名
     *
     * @return  RequestFile
     */
    public function file(string $name): RequestFile
    {
        if (!isset($this->_files[$name])) {
            return null;
        }
        return new RequestFile($this->_files[$name]);
    }

    /**
     * 判断文件是否存在
     *
     * @param   string  $name  文件名
     *
     * @return  bool
     */
    public function hasFile(string $name): bool
    {
        return isset($this->_files[$name]);
    }

    /**
     * 正则匹配 PATH
     *
     * @param   string  $regex  正则表达式
     *
     * @return  int|false
     */
    public function pattern(string $regex)
    {
        return preg_match($regex, $this->path());
    }

    /**
     * 判断是否是 Ajax 请求
     *
     * @return  bool
     */
    public function ajax(): bool
    {
        return $this->hasHeader('X-Requested-With');
    }

    public function __get(string $name)
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
