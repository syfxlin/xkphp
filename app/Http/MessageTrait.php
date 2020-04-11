<?php

namespace App\Http;

use RuntimeException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $headerAlias = [];

    /**
     * @var string
     */
    private $protocol = '1.1';

    /**
     * @var Stream
     */
    private $stream;

    /**
     * @inheritDoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        if (!in_array($version, ['1.0', '1.1', '2', '3'], true)) {
            throw new RuntimeException(
                "Unsupported HTTP protocol version \"$version\" provided"
            );
        }
        $new = clone $this;
        $new->protocol = $version;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name): bool
    {
        return isset($this->headerAlias[strtolower($name)]);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name): array
    {
        if (!$this->hasHeader($name)) {
            return [];
        }
        return $this->headers[$this->headerAlias[strtolower($name)]];
    }

    protected function setHeaders(array $headers): void
    {
        $this->headers = $headers;
        foreach ($headers as $key => $value) {
            $this->headerAlias[strtolower($key)] = $key;
        }
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $new = clone $this;
        $new->headerAlias[strtolower($name)] = $name;
        $new->headers[$name] = is_array($value) ? $value : [$value];
        if (strtolower($name) === 'set-cookie') {
            $new->cookies = Cookie::makeFromArray($new->headers[$name]);
        }
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {
        return $this->withHeader(
            $name,
            array_merge($this->getHeader($name), $value)
        );
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }
        $new = clone $this;
        $low_name = strtolower($name);
        $real_name = $new->headerAlias[$low_name];
        unset($new->headerAlias[$low_name], $new->headers[$real_name]);
        if (strtolower($name) === 'set-cookie') {
            $new->cookies = [];
        }
    }

    public function withHeaders(array $headers)
    {
        $result = clone $this;
        foreach ($headers as $name => $value) {
            $result = $result->withHeader($name, $value);
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->stream = $body;
        return $new;
    }
}
