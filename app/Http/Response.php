<?php

namespace App\Http;

use App\Contracts\Renderable;
use DateTimeInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function is_string;
use function json_encode;
use function method_exists;

class Response implements ResponseInterface
{
    use MessageTrait;

    /**
     * @var array
     */
    public static $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated to 306 => '(Unused)'
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    /**
     * @var string
     */
    private $reason_phrase;

    /**
     * @var int
     */
    private $status;

    /**
     * @var array<string, Cookie>
     */
    private $cookies;

    /**
     * Response constructor.
     * @param string|StreamInterface $content
     * @param int $status
     * @param array $headers
     */
    public function __construct(
        $content = '',
        int $status = 200,
        array $headers = []
    ) {
        if ($content === null) {
            $content = '';
        }
        $this->status = $status;
        $this->reason_phrase = '';
        $this->setHeaders($headers);
        if (!$content instanceof StreamInterface) {
            $this->stream = Stream::make(
                $this->convert($content),
                'php://temp',
                'wb+'
            );
        } else {
            $this->stream = $content;
        }
        $this->cookies = Cookie::makeFromArray($this->getHeader('Set-Cookie'));
    }

    /**
     * @param string $text
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function text(
        string $text,
        int $status = 200,
        array $headers = []
    ): Response {
        return (new static($text, $status, $headers))->setHeader(
            'Content-Type',
            'text/plain; charset=utf-8'
        );
    }

    /**
     * @param string $html
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function html(
        string $html,
        int $status = 200,
        array $headers = []
    ): Response {
        return (new static($html, $status, $headers))->setHeader(
            'Content-Type',
            'text/html; charset=utf-8'
        );
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return Response
     */
    public static function json(
        $data,
        int $status = 200,
        array $headers = [],
        int $options = 0
    ): Response {
        return (new static(
            json_encode($data, $options),
            $status,
            $headers
        ))->setHeader('Content-Type', 'application/json');
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function redirect(
        string $url,
        int $status = 302,
        array $headers = []
    ): Response {
        return (new static('', $status, $headers))->setHeader('Location', $url);
    }

    /**
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public static function make(
        $content = '',
        int $status = 200,
        array $headers = []
    ): Response {
        return new static($content, $status, $headers);
    }

    public function send(): void
    {
        (new SapiEmitter())->emit($this);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function setStatus($code, $reasonPhrase = ''): self
    {
        $this->status = $code;
        $this->reason_phrase =
            $reasonPhrase !== '' ? $reasonPhrase : self::$phrases[$code] ?? '';
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        return $new->setStatus($code, $reasonPhrase);
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase(): string
    {
        return $this->reason_phrase;
    }

    /**
     * 获取响应内容
     *
     * @return  string
     */
    public function getContent(): string
    {
        return $this->stream->getContents();
    }

    /**
     * 获取响应码
     *
     * @return  int
     */
    public function getStatus(): int
    {
        return $this->getStatusCode();
    }

    /**
     * 设置响应内容
     *
     * @param   mixed  $content  响应内容
     *
     * @return  ResponseInterface
     */
    public function content($content = ''): ResponseInterface
    {
        $new = clone $this;
        $new->stream->setContents($this->convert($content));
        return $new;
    }

    /**
     * 设置响应码
     *
     * @param   int       $code  响应码
     *
     * @return  ResponseInterface
     */
    public function status(int $code = 200): ResponseInterface
    {
        return $this->setStatus($code);
    }

    /**
     * 设置响应头
     *
     * @param   string    $key    响应头的键
     * @param   string|string[]    $value  响应头的值
     *
     * @return  Response
     */
    public function header(string $key, $value): Response
    {
        return $this->setHeader($key, $value);
    }

    /**
     * @param string $name
     * @return Cookie
     */
    public function getCookie(string $name): ?Cookie
    {
        return $this->cookies[$name] ?? null;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    private function updateCookieHeader(): void
    {
        // Set Set-Cookie header
        if ($this->cookies !== []) {
            $this->headers['Set-Cookie'] = Cookie::makeToArray($this->cookies);
            $this->headerAlias['set-cookie'] = 'Set-Cookie';
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasCookie(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    public function setCookie(Cookie $cookie): self
    {
        $this->cookies[$cookie->getName()] = $cookie;
        $this->updateCookieHeader();
        return $this;
    }

    /**
     * @param Cookie $cookie
     * @return Response
     */
    public function withCookie(Cookie $cookie): self
    {
        $new = clone $this;
        return $new->setCookie($cookie);
    }

    /**
     * @param string $name
     * @return Response
     */
    public function withoutCookie(string $name): self
    {
        $new = clone $this;
        unset($new->cookies[$name]);
        // Set Set-Cookie header
        $new->updateCookieHeader();
        return $new;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int|string|DateTimeInterface $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $http_only
     * @return Response
     */
    public function cookie(
        string $name,
        string $value = '',
        int $expire = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $http_only = false
    ): Response {
        return $this->setCookie(
            Cookie::make($name, $value)
                ->setMaxAge($expire)
                ->setPath($path)
                ->setDomain($domain)
                ->setSecure($secure)
                ->setHttpOnly($http_only)
        );
    }

    /**
     * @param Cookie[] $cookies
     * @return $this
     */
    public function setCookies(array $cookies): self
    {
        foreach ($cookies as $cookie) {
            $this->setCookie($cookie);
        }
        return $this;
    }

    /**
     * @param Cookie[] $cookies
     * @return Response
     */
    public function withCookies(array $cookies): Response
    {
        $result = clone $this;
        return $result->setCookies($cookies);
    }

    /**
     * @param string[] $cookies
     * @return $this
     */
    public function removeCookies(array $cookies): self
    {
        foreach ($cookies as $cookie) {
            unset($this->cookies[$cookie]);
        }
        $this->updateCookieHeader();
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
        if ($content === null) {
            return '';
        }
        // String
        if (is_string($content)) {
            return $content;
        }
        // Renderable
        if ($content instanceof Renderable) {
            return $content->render();
        }
        // Has __toString
        if (method_exists($content, '__toString')) {
            return $content->__toString();
        }
        // JSON
        $this->headers['Content-type'] = ['application/json'];
        return json_encode($content);
    }
}
