<?php

namespace App\Kernel;

use App\Application;
use App\Facades\Crypt;
use App\Facades\Request;

class Response
{
    private $content = '';
    private $code = 200;
    private $headers = [];
    private $cookies = [];

    /**
     * 不返回错误页的 HTTP Code
     *
     * @var array
     */
    private $accept_code = [200];

    public static function getInstance(): Response
    {
        return Application::getInstance(self::class);
    }

    /**
     * 构造响应
     *
     * @param   mixed     $content  响应内容
     * @param   int       $code     响应码
     * @param   array     $headers  响应头
     *
     * @return  Response
     */
    public function make($content = '', int $code = 200, array $headers = []): Response
    {
        $this->code = $code;
        $this->headers = array_merge($this->headers, $headers);
        $this->content = $this->convert($content);
        return $this;
    }

    /**
     * 获取响应内容
     *
     * @return  string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * 获取响应码
     *
     * @return  int
     */
    public function getStatus(): int
    {
        return $this->code;
    }

    /**
     * 设置响应内容
     *
     * @param   mixed  $content  响应内容
     *
     * @return  Response
     */
    public function content($content = ''): Response
    {
        $this->content = $this->convert($content);
        return $this;
    }

    /**
     * 设置响应码
     *
     * @param   int       $code  响应码
     *
     * @return  Response
     */
    public function status(int $code = 200): Response
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 转换响应内容
     *
     * @param   mixed   $content  响应内容
     *
     * @return  string
     */
    private function convert($content): string
    {
        if (!in_array($this->code, $this->accept_code) && !Request::ajax()) {
            return view('errors/errors', $content)->render();
        }
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

    /**
     * 设置响应头
     *
     * @param   string    $key    响应头的键
     * @param   string    $value  响应头的值
     *
     * @return  Response
     */
    public function header(string $key, string $value): Response
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * 设置多个响应头
     *
     * @param   array     $headers  多个响应头
     *
     * @return  Response
     */
    public function withHeaders(array $headers): Response
    {
        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }
        return $this;
    }

    /**
     * 取得响应头
     *
     * @param   string  $key  响应头的键
     *
     * @return  string
     */
    public function getHeader(string $key): string
    {
        return $this->headers[$key];
    }

    /**
     * 获取所有响应头
     *
     * @return  array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 设置 Cookie
     *
     * @param   string    $name      Cookie 名称
     * @param   string    $value     Cookie 值
     * @param   int       $expire    过期时间
     * @param   string    $path      有效路径
     * @param   string    $domain    有效域名
     * @param   bool      $secure    是否通过安全传输
     * @param   bool      $httponly  HTTP Only
     *
     * @return  Response
     */
    public function cookie(
        string $name,
        string $value = "",
        int $expire = 0,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ): Response {
        $this->cookies[$name] = [
            'name' => $name,
            'value' => Crypt::encrypt($value),
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly
        ];
        return $this;
    }

    /**
     * 设置多个 Cookies
     *
     * @param   array     $cookies  Cookies
     *
     * @return  Response
     */
    public function withCookies(array $cookies): Response
    {
        foreach ($cookies as $value) {
            $this->cookie(
                $value['name'],
                $value['value'] ?? "",
                $value['expire'] ?? 0,
                $value['path'] ?? "",
                $value['domain'] ?? "",
                $value['secure'] ?? false,
                $value['httponly'] ?? false
            );
        }
        return $this;
    }

    /**
     * 获取 Cookie
     *
     * @param   string  $key  Cookie 的名称
     *
     * @return  string
     */
    public function getCookie(string $key): string
    {
        return $this->cookies[$key];
    }

    /**
     * 获取所有 Cookies
     *
     * @return  array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * JSON 响应
     *
     * @param   mixed     $data     响应的数据
     * @param   int       $code     响应码
     * @param   array     $headers  响应头
     *
     * @return  Response
     */
    public function json($data, int $code = 200, array $headers = []): Response
    {
        $this->content = json_encode($data);
        $this->status($code);
        $this->header('Content-type', 'application/json');
        $this->withHeaders($headers);
        return $this;
    }

    /**
     * 发送响应
     *
     * @return  void
     */
    public function emit(): void
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

    /**
     * 发送下载响应
     *
     * @param   string       $pathToFile  文件位置
     * @param   string|null  $name        文件名
     * @param   array        $headers     响应头
     *
     * @return  void
     */
    public function download(string $pathToFile, $name = null, array $headers = []): void
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
