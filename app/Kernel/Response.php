<?php

namespace App\Kernel;

use App\Application;

class Response
{
    private $content;
    private $code;
    private $headers;
    private $cookies;

    public static function getInstance($content = '', $code = 200)
    {
        return Application::getInstance(self::class, $content, $code);
    }

    public function __construct($content = '', $code = 200)
    {
        $this->code = $code;
        $this->content = $this->convert($content);
        $this->headers = [];
        $this->cookies = [];
    }

    private function convert($content)
    {
        if (is_string($content)) {
            return $content;
        }
        if (is_object($content) && get_class($content) === 'App\Kernel\ViewItem') {
            return $content->render();
        }
        return json_encode($content);
    }

    public function header(string $key, string $value): Response
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withHeaders(array $headers): Response
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    public function cookie(
        $name,
        $value = "",
        $expire = 0,
        $path = "",
        $domain = "",
        $secure = false,
        $httponly = false
    ): Response {
        $this->cookies[$name] = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly
        ];
        return $this;
    }

    public function emit()
    {
        http_response_code($this->code);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        foreach ($this->cookies as $name => $value) {
            setcookie(
                $value['name'],
                $value['value'],
                $value['expire'],
                $value['path'],
                $value['domain'],
                $value['secure'],
                $value['httponly']
            );
        }
        echo $this->content;
    }
}
