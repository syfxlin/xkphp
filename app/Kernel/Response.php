<?php

namespace App\Kernel;

use App\Application;

class Response
{
    private $content = '';
    private $code = 200;
    private $headers = [];
    private $cookies = [];

    public static function getInstance($content = '', $code = 200, $headers = [])
    {
        return Application::getInstance(self::class, $content, $code, $headers);
    }

    public function __construct($content = '', $code = 200, $headers = [])
    {
        $this->make($content, $code, $headers);
    }

    public function make($content = '', $code = 200, $headers = [])
    {
        $this->content = $this->convert($content);
        $this->code = $code;
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getStatus()
    {
        return $this->code;
    }

    public function content($content = null)
    {
        $this->content = $this->convert($content);
        return $this;
    }

    public function status($code = 200)
    {
        $this->code = $code;
        return $this;
    }

    private function convert($content)
    {
        if (is_null($content)) {
            return '';
        }
        // String
        if (is_string($content)) {
            return $content;
        }
        // View
        if (is_object($content) && get_class($content) === \App\Kernel\View::class) {
            return $content->render();
        }
        // JSON
        $this->header('Content-type', 'application/json');
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
            $this->header($key, $value);
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

    public function withCookies(array $headers): Response
    {
        foreach ($headers as $value) {
            $this->cookie(
                $value['name'],
                $value['value'],
                $value['expire'],
                $value['path'],
                $value['domain'],
                $value['secure'],
                $value['httponly']
            );
        }
        return $this;
    }

    public function json($data, $code = 200, $headers = [])
    {
        $this->content = json_encode($data);
        $this->status($code);
        $this->header('Content-type', 'application/json');
        $this->withHeaders($headers);
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

    public function download($pathToFile, $name = null, $headers = [])
    {
        $name = $name ?? basename($pathToFile);
        $this->withHeaders(array_merge([
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"$name\"",
        ], $headers));
        ob_start();
        // TODO: 利用X-Sendfile
        readfile($pathToFile);
        $this->emit();
        exit;
    }
}
