<?php

namespace App\Http;

use App\Exceptions\Http\UriParseException;
use App\Exceptions\Http\UriTypeException;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use function array_pad;
use function explode;
use function implode;
use function is_int;
use function is_string;
use function parse_url;
use function strtolower;
use function urldecode;
use function urlencode;

class Uri implements UriInterface
{
    /**
     * @var string
     */
    private $scheme;
    /**
     * @var string
     */
    private $host;
    /**
     * @var int|null
     */
    private $port;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $query;
    /**
     * @var string
     */
    private $fragment;
    /**
     * @var string
     */
    private $user_info;

    public function __construct(string $uri = '')
    {
        if ('' === $uri) {
            return;
        }
        $this->parseUri($uri);
    }

    private function parseUri(string $uri): void
    {
        $parts = parse_url($uri);

        if ($parts === false) {
            throw new UriParseException(
                'The source URI string appears to be malformed'
            );
        }

        $this->scheme = isset($parts['scheme'])
            ? strtolower($parts['scheme'])
            : '';
        $this->user_info = isset($parts['user'])
            ? $this->encodingUrl($parts['user'])
            : '';
        $this->host = isset($parts['host']) ? strtolower($parts['host']) : '';
        $this->port = $parts['port'] ?? null;
        $this->path = isset($parts['path'])
            ? $this->parsePath($parts['path'])
            : '';
        $this->query = isset($parts['query'])
            ? $this->parseQuery($parts['query'])
            : '';
        $this->fragment = isset($parts['fragment'])
            ? $this->encodingUrl($parts['fragment'])
            : '';

        if (isset($parts['pass'])) {
            $this->user_info .= ':' . $parts['pass'];
        }
    }

    private function encodingUrl(string $url): string
    {
        return urlencode(urldecode($url));
    }

    private function parsePath(string $path): string
    {
        $paths = explode('/', $path);
        foreach ($paths as $index => $value) {
            $paths[$index] = $this->encodingUrl($value);
        }
        return implode('/', $paths);
    }

    private function parseQuery(string $query): string
    {
        $parts = explode('&', $query);
        foreach ($parts as $index => $part) {
            [$key, $value] = array_pad(explode('=', $part), 2, null);
            $parts[$index] = $this->encodingUrl($key);
            if ($value !== null) {
                $parts[$index] .= '=' . $this->encodingUrl($value);
            }
        }
        return implode('&', $parts);
    }

    /**
     * @inheritDoc
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @inheritDoc
     */
    public function getAuthority(): string
    {
        $auth = $this->user_info === '' ? '' : $this->user_info . '@';
        $auth .= $this->host;
        $auth .= $this->port === null ? '' : ':' . $this->port;
        return $auth;
    }

    /**
     * @inheritDoc
     */
    public function getUserInfo(): string
    {
        return $this->user_info;
    }

    /**
     * @inheritDoc
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function setScheme($scheme): UriInterface
    {
        if (!is_string($scheme)) {
            throw new UriTypeException('scheme expects a string argument');
        }
        $scheme = strtolower($scheme);
        $this->scheme = $scheme;
        return $this;
    }

    public function setUserInfo($user, $password = null): UriInterface
    {
        if (
            !is_string($user) ||
            ($password !== null && !is_string($password))
        ) {
            throw new UriTypeException(
                'user or password expects a string argument'
            );
        }
        $user = $this->encodingUrl($user);
        $this->user_info = $user;
        if ($password !== null) {
            $this->user_info .= ':' . $password;
        }
        return $this;
    }

    public function setHost($host): UriInterface
    {
        if (!is_string($host)) {
            throw new UriTypeException('host expects a string argument');
        }
        $host = strtolower($host);
        $this->host = $host;
        return $this;
    }

    public function setPort($port): UriInterface
    {
        if (!is_int($port) && $port !== null) {
            throw new UriTypeException('port expects a string argument');
        }
        $this->port = $port;
        return $this;
    }

    public function setPath($path): UriInterface
    {
        if (!is_string($path)) {
            throw new UriTypeException('path expects a string argument');
        }
        $path = $this->parsePath($path);
        $this->path = $path;
        return $this;
    }

    public function setQuery($query): UriInterface
    {
        if (!is_string($query)) {
            throw new UriTypeException('query expects a string argument');
        }
        $query = $this->parseQuery($query);
        $this->query = $query;
        return $this;
    }

    public function setFragment($fragment): UriInterface
    {
        if (!is_string($fragment)) {
            throw new UriTypeException('fragment expects a string argument');
        }
        $fragment = $this->encodingUrl($fragment);
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withScheme($scheme): UriInterface
    {
        $new = clone $this;
        return $new->setScheme($scheme);
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = null): UriInterface
    {
        $new = clone $this;
        return $new->setUserInfo($user, $password);
    }

    /**
     * @inheritDoc
     */
    public function withHost($host): UriInterface
    {
        $new = clone $this;
        return $new->setHost($host);
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): UriInterface
    {
        $new = clone $this;
        return $new->setPort($port);
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): UriInterface
    {
        $new = clone $this;
        return $new->setPath($path);
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query): UriInterface
    {
        $new = clone $this;
        return $new->setQuery($query);
    }

    /**
     * @inheritDoc
     */
    public function withFragment($fragment): UriInterface
    {
        $new = clone $this;
        return $new->setFragment($fragment);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $uri = '';
        $uri .= $this->getScheme();
        $uri .= '://';
        $uri .= $this->getAuthority();
        $uri .= $this->getPath();
        if ($this->getQuery() !== '') {
            $uri .= '?' . $this->getQuery();
        }
        if ($this->getFragment() !== '') {
            $uri .= '#' . $this->getFragment();
        }
        return $uri;
    }
}
