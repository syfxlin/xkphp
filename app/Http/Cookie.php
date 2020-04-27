<?php

namespace App\Http;

use DateTimeInterface;
use function array_map;
use function array_pad;
use function array_shift;
use function explode;
use function gmdate;
use function implode;
use function preg_split;
use function strtolower;
use function strtotime;
use function urlencode;

class Cookie
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $value;
    /**
     * @var int
     */
    private $expires = 0;
    /**
     * @var int
     */
    private $max_age = 0;
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var string|null
     */
    private $domain;
    /**
     * @var bool
     */
    private $secure = false;
    /**
     * @var bool
     */
    private $http_only = false;

    public function __construct(string $name, string $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function make(string $name, string $value = null): Cookie
    {
        return new static($name, $value);
    }

    public static function makeFromString(string $cookie_str): Cookie
    {
        $commands = preg_split('/;\\s*/', $cookie_str);
        [$name, $value] = array_pad(
            explode('=', array_shift($commands)),
            2,
            null
        );
        $cookie = new static($name, $value);
        foreach ($commands as $command) {
            [$com_key, $com_value] = array_pad(explode('=', $command), 2, null);
            switch (strtolower($com_key)) {
                case 'expires':
                    $cookie = $cookie->setExpires($com_value);
                    break;
                case 'max-age':
                    $cookie = $cookie->setMaxAge((int) $com_value);
                    break;
                case 'domain':
                    $cookie = $cookie->setDomain($com_value);
                    break;
                case 'path':
                    $cookie = $cookie->setPath($com_value);
                    break;
                case 'secure':
                    $cookie = $cookie->setSecure(true);
                    break;
                case 'httponly':
                    $cookie = $cookie->setHttpOnly(true);
                    break;
            }
        }
        return $cookie;
    }

    public static function makeFromArray(array $cookies_arr): array
    {
        $cookies = [];
        foreach ($cookies_arr as $cookie_str) {
            $cookie = static::makeFromString($cookie_str);
            $cookies[$cookie->getName()] = $cookie;
        }
        return $cookies;
    }

    /**
     * @param Cookie[] $cookies
     * @return array
     */
    public static function makeToArray(array $cookies): array
    {
        return array_map(static function (Cookie $cookie) {
            return $cookie->__toString();
        }, $cookies);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getExpires(): int
    {
        return $this->expires;
    }

    public function getMaxAge(): int
    {
        return $this->max_age;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getSecure(): bool
    {
        return $this->secure;
    }

    public function getHttpOnly(): bool
    {
        return $this->http_only;
    }

    public function setValue(string $value = null): Cookie
    {
        $this->value = $value;
        return $this;
    }

    public function setExpires($expires = 0): Cookie
    {
        if ($expires instanceof DateTimeInterface) {
            $expires = $expires->getTimestamp();
        } else {
            $expires = (int) strtotime($expires);
        }
        $this->expires = $expires;
        return $this;
    }

    public function setMaxAge(?int $max_age = null): Cookie
    {
        $this->max_age = (int) $max_age;
        return $this;
    }

    public function setPath(?string $path = null): Cookie
    {
        $this->path = $path;
        return $this;
    }

    public function setDomain(?string $domain = null): Cookie
    {
        $this->domain = $domain;
        return $this;
    }

    public function setSecure(bool $secure = true): Cookie
    {
        $this->secure = $secure;
        return $this;
    }

    public function setHttpOnly(bool $http_only = true): Cookie
    {
        $this->http_only = $http_only;
        return $this;
    }

    public function withValue(string $value = null): Cookie
    {
        $new = clone $this;
        return $new->setValue($value);
    }

    public function withExpires($expires = 0): Cookie
    {
        if ($expires instanceof DateTimeInterface) {
            $expires = $expires->getTimestamp();
        } else {
            $expires = (int) strtotime($expires);
        }
        $new = clone $this;
        return $new->setExpires($expires);
    }

    public function withMaxAge(?int $max_age = null): Cookie
    {
        $new = clone $this;
        return $new->setMaxAge($max_age);
    }

    public function withPath(?string $path = null): Cookie
    {
        $new = clone $this;
        return $new->setPath($path);
    }

    public function withDomain(?string $domain = null): Cookie
    {
        $new = clone $this;
        return $new->setDomain($domain);
    }

    public function withSecure(bool $secure = true): Cookie
    {
        $new = clone $this;
        return $new->setSecure($secure);
    }

    public function withHttpOnly(bool $http_only = true): Cookie
    {
        $new = clone $this;
        return $new->setHttpOnly($http_only);
    }

    public function __toString()
    {
        $cookie = [];
        $cookie[] = urlencode($this->name) . '=' . urlencode($this->value);
        if ($this->expires) {
            $expires = gmdate('D, d M Y H:i:s T', $this->expires);
            $cookie[] = "Expires=$expires";
        }
        if ($this->max_age) {
            $cookie[] = "Max-Age=$this->max_age";
        }
        if ($this->domain) {
            $cookie[] = "Domain=$this->domain";
        }
        if ($this->path) {
            $cookie[] = "Path=$this->path";
        }
        if ($this->secure) {
            $cookie[] = 'Secure';
        }
        if ($this->http_only) {
            $cookie[] = 'HttpOnly';
        }
        return implode('; ', $cookie);
    }
}
