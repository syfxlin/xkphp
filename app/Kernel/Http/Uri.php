<?php

namespace App\Kernel\Http;

use Psr\Http\Message\UriInterface;
use RuntimeException;

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
            throw new RuntimeException(
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

    /**
     * @inheritDoc
     */
    public function withScheme($scheme): UriInterface
    {
        if (!is_string($scheme)) {
            throw new RuntimeException('scheme expects a string argument');
        }
        $scheme = strtolower($scheme);
        if ($scheme === $this->scheme) {
            return $this;
        }
        $new = clone $this;
        $new->scheme = $scheme;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withUserInfo($user, $password = null): UriInterface
    {
        if (
            !is_string($user) ||
            ($password !== null && !is_string($password))
        ) {
            throw new RuntimeException(
                'user or password expects a string argument'
            );
        }
        $user = $this->encodingUrl($user);
        $new = clone $this;
        $new->user_info = $user;
        if ($password !== null) {
            $new->user_info .= ':' . $password;
        }
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withHost($host): UriInterface
    {
        if (!is_string($host)) {
            throw new RuntimeException('host expects a string argument');
        }
        $host = strtolower($host);
        if ($host === $this->host) {
            return $this;
        }
        $new = clone $this;
        $new->host = $host;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPort($port): UriInterface
    {
        if (!is_int($port) && $port !== null) {
            throw new RuntimeException('port expects a string argument');
        }
        if ($port === $this->port) {
            return $this;
        }
        $new = clone $this;
        $new->port = $port;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withPath($path): UriInterface
    {
        if (!is_string($path)) {
            throw new RuntimeException('path expects a string argument');
        }
        $path = $this->parsePath($path);
        if ($path === $this->path) {
            return $this;
        }
        $new = clone $this;
        $new->path = $path;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withQuery($query): UriInterface
    {
        if (!is_string($query)) {
            throw new RuntimeException('query expects a string argument');
        }
        $query = $this->parseQuery($query);
        if ($query === $this->query) {
            return $this;
        }
        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withFragment($fragment): UriInterface
    {
        if (!is_string($fragment)) {
            throw new RuntimeException('fragment expects a string argument');
        }
        $fragment = $this->encodingUrl($fragment);
        if ($fragment === $this->fragment) {
            return $this;
        }
        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
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
