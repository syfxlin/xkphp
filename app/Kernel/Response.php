<?php

namespace App\Kernel;

use App\Application;

class Response
{
    public $content = null;

    public static function getInstance($content = '', $code = 200)
    {
        return Application::getInstance(self::class, $content, $code);
    }

    public function __construct($content = '', $code = 200)
    {
        http_response_code($code);
        $this->content = is_string($content) ? $content : json_encode($content);
    }

    public function header(string $key, string $value): Response
    {
        header("$key: $value");
        return $this;
    }

    public function withHeaders(array $headers): Response
    {
        foreach ($headers as $key => $value) {
            header("$key: $value");
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
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        return $this;
    }
}
