<?php

namespace App\Http;

use App\Exceptions\Http\InvalidUploadFileException;
use App\Exceptions\Http\UnsupportedSetRequestException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use function array_merge;
use function count;
use function getDotData;
use function is_array;
use function is_string;
use function json_decode;
use function preg_match;
use function session;
use function stripos;
use function strpos;
use function strtoupper;

class Request implements ServerRequestInterface
{
    use RequestTrait;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $cookies;

    /**
     * @var null|array|object
     */
    private $parsed_body;

    /**
     * @var array
     */
    private $query;

    /**
     * @var array
     */
    private $server;

    /**
     * @var array
     */
    private $files;

    /**
     * Request constructor.
     * @param array $server
     * @param array $files
     * @param string|UriInterface $uri
     * @param string $method
     * @param string|StreamInterface $body
     * @param array $headers
     * @param array $cookies
     * @param array $query
     * @param null|array $parsed_body
     * @param string $protocol
     */
    public function __construct(
        array $server = [],
        array $files = [],
        $uri = '',
        string $method = 'GET',
        $body = 'php://input',
        array $headers = [],
        array $cookies = [],
        array $query = [],
        $parsed_body = null,
        string $protocol = '1.1'
    ) {
        $this->validateFiles($files);
        if ($body === 'php://input') {
            $body = new Stream($body);
        }
        $this->setMethod($method);
        if ($uri instanceof UriInterface) {
            $this->uri = $uri;
        } else {
            $this->uri = new Uri($uri);
        }
        if ($body instanceof StreamInterface) {
            $this->stream = $body;
        } else {
            $this->stream = new Stream($body, 'wb+');
        }
        $this->setHeaders($headers);
        $this->server = $server;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->query = $query;
        $this->protocol = $protocol;

        $content_type = $this->header('Content-Type');
        if (
            $content_type !== null &&
            stripos($content_type, 'application/json') !== false
        ) {
            $this->parsed_body = array_merge(
                $parsed_body,
                json_decode($this->stream->getContents(), true)
            );
        } else {
            $this->parsed_body = $parsed_body;
        }

        if (!$this->hasHeader('Host') && $this->uri->getHost()) {
            $host = $this->uri->getHost();
            $host .= $this->uri->getPort() ? ':' . $this->uri->getPort() : '';
            $this->headerAlias['host'] = 'Host';
            $this->headers['Host'] = [$host];
        }
    }

    /**
     * @param array|null $server
     * @param array|null $query
     * @param array|null $body
     * @param array|null $cookies
     * @param array|null $files
     * @return Request
     */
    public static function make(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ): Request {
        $files = Functions::convertFiles($files ?: $_FILES);
        $server = $server ?: $_SERVER;
        $uri =
            isset($server['HTTPS']) && $server['HTTPS'] === 'on'
                ? 'https://'
                : 'http://';
        if (isset($server['HTTP_HOST'])) {
            $uri .= $server['HTTP_HOST'];
        } else {
            $uri .=
                $server['SERVER_NAME'] .
                (isset($server['SERVER_PORT']) &&
                $server['SERVER_PORT'] !== '80' &&
                $server['SERVER_PORT'] !== '443'
                    ? ':' . $server['SERVER_PORT']
                    : '');
        }
        $uri .= $server['REQUEST_URI'];
        $protocol = '1.1';
        if (isset($server['SERVER_PROTOCOL'])) {
            preg_match(
                '|^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$|',
                $server['SERVER_PROTOCOL'],
                $matches
            );
            $protocol = $matches['version'];
        }
        return new static(
            $server,
            $files,
            $uri,
            $server['REQUEST_METHOD'],
            'php://input',
            Functions::parseHeaders($server),
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            $protocol
        );
    }

    /**
     * @inheritDoc
     */
    public function getServerParams(): array
    {
        return $this->server;
    }

    public function setServerParams(array $server): self
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    public function setCookieParams(array $cookies): self
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        return $new->setCookieParams($cookies);
    }

    /**
     * @inheritDoc
     */
    public function getQueryParams(): array
    {
        return $this->query;
    }

    public function setQueryParams(array $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withQueryParams(array $query)
    {
        $new = clone $this;
        return $new->setQueryParams($query);
    }

    /**
     * @inheritDoc
     */
    public function getUploadedFiles(): array
    {
        return $this->files;
    }

    public function setUploadedFiles(array $uploaded_files): self
    {
        $this->validateFiles($uploaded_files);
        $this->files = $uploaded_files;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withUploadedFiles(array $uploaded_files)
    {
        $new = clone $this;
        return $new->setUploadedFiles($uploaded_files);
    }

    /**
     * @inheritDoc
     */
    public function getParsedBody()
    {
        return $this->parsed_body;
    }

    public function setParsedBody($data): self
    {
        $this->parsed_body = $data;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        return $new->setParsedBody($data);
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attrs): self
    {
        $this->attributes = $attrs;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($name, $default = null)
    {
        if (!isset($this->attributes[$name])) {
            return $default;
        }
        return $this->attributes[$name];
    }

    public function setAttribute($name, $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withAttribute($name, $value)
    {
        $new = clone $this;
        return $new->setAttribute($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function withoutAttribute($name)
    {
        $new = clone $this;
        unset($new->attributes[$name]);
        return $new;
    }

    /**
     * @param array $uploaded_files
     */
    protected function validateFiles(array $uploaded_files): void
    {
        foreach ($uploaded_files as $file) {
            if (is_array($file)) {
                $this->validateFiles($file);
            } elseif (!$file instanceof UploadedFileInterface) {
                throw new InvalidUploadFileException(
                    'Invalid leaf in uploaded files structure'
                );
            }
        }
    }

    /**
     * 获取 SERVER 参数
     *
     * @param   string       $name     要获取的 SERVER 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null
     */
    public function server(string $name, $default = null): ?string
    {
        return $this->server[$name] ?? $default;
    }

    /**
     * 获取 Header 的值
     *
     * @param   string       $name     要获取的 Header 的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|null|array
     */
    public function header(string $name, $default = null)
    {
        $header = $this->getHeader($name);
        if ($header === []) {
            return $default;
        }
        if (count($header) === 1) {
            return $header[0];
        }
        return $header;
    }

    /**
     * 获取所有的参数
     *
     * @return  array
     */
    public function all(): array
    {
        return array_merge(
            $this->getQueryParams(),
            $this->getParsedBody(),
            $this->getAttributes(),
            $this->getCookieParams(),
            $this->getUploadedFiles()
        );
    }

    /**
     * 获取 POST 参数
     *
     * @param   string       $key     要获取的 POST 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|array|null
     */
    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $this->parsed_body;
        }
        if (isset($key) && strpos($key, '.') !== false) {
            return getDotData($key, $this->parsed_body);
        }
        if (!isset($this->parsed_body[$key])) {
            return $default;
        }
        return $this->parsed_body[$key];
    }

    /**
     * 获取 GET 参数
     *
     * @param   string       $key     要获取的 GET 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|array|null
     */
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        if (!isset($this->query[$key])) {
            return $default;
        }
        return $this->query[$key];
    }

    /**
     * 判断参数是否存在
     *
     * @param   string|array  $key  参数名称
     *
     * @return  bool
     */
    public function has($key): bool
    {
        if (is_string($key)) {
            return $this->__isset($key);
        }

        foreach ($key as $value) {
            if (!$this->__isset($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取 Cookie 参数
     *
     * @param   string       $key     要获取的 Cookie 参数的名称
     * @param   string|null  $default  默认值
     *
     * @return  string|array|null
     */
    public function cookie($key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookies;
        }
        if (!isset($this->cookies[$key])) {
            return $default;
        }
        return $this->cookies[$key];
    }

    /**
     * 获取 SessionManager 实例或者值
     *
     * @param   string|null|array  $name     SessionManager 名称
     * @param   string|null  $default  默认值
     *
     * @return  mixed
     */
    public function session($name = null, $default = null)
    {
        return session($name, $default);
    }

    /**
     * 获取当前的 PATH
     *
     * @return  string
     */
    public function path(): string
    {
        return $this->getUri()->getPath();
    }

    /**
     * 获取当前请求的URL
     *
     * @return  string
     */
    public function url(): string
    {
        $uri = $this->getUri();
        return $uri->getScheme() .
            '://' .
            $uri->getAuthority() .
            $uri->getPath();
    }

    /**
     * 获取完整的请求 URL
     *
     * @return  string
     */
    public function fullUrl(): string
    {
        return $this->getUri()->__toString();
    }

    /**
     * 获取当前请求的方法
     *
     * @return  string
     */
    public function method(): string
    {
        return $this->getMethod();
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
        return strtoupper($method) === $this->getMethod();
    }

    /**
     * 获取上传的文件
     *
     * @param   string  $name  上传文件名
     *
     * @return  UploadedFileInterface
     */
    public function file(string $name): ?UploadedFileInterface
    {
        if (!isset($this->files[$name])) {
            return null;
        }
        return $this->files[$name];
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
        return isset($this->files[$name]);
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
        if (isset($this->query[$name])) {
            return $this->query[$name];
        }
        if (isset($this->parsed_body[$name])) {
            return $this->parsed_body[$name];
        }
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        if (isset($this->cookies[$name])) {
            return $this->cookies[$name];
        }
        if (isset($this->files[$name])) {
            return $this->file($name);
        }
        return null;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __isset($name)
    {
        return isset($this->query[$name]) ||
            isset($this->parsed_body[$name]) ||
            isset($this->attributes[$name]) ||
            isset($this->cookies[$name]) ||
            isset($this->files[$name]);
    }
}
