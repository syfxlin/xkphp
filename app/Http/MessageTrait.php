<?php

namespace App\Http;

use App\Exceptions\Http\UnsupportedHttpProtocolException;
use RuntimeException;
use Psr\Http\Message\StreamInterface;
use function array_merge;
use function implode;
use function in_array;
use function is_array;
use function strtolower;

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

    public function setProtocolVersion($version): self
    {
        if (!in_array($version, ['1.0', '1.1', '2', '3'], true)) {
            throw new UnsupportedHttpProtocolException(
                "Unsupported HTTP protocol version \"$version\" provided"
            );
        }
        $this->protocol = $version;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version): self
    {
        $new = clone $this;
        return $new->setProtocolVersion($version);
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
        $result = $this->headers[$this->headerAlias[strtolower($name)]];
        return is_array($result) ? $result : [$result];
    }

    public function setHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function setHeader($name, $value): self
    {
        $this->headerAlias[strtolower($name)] = $name;
        $this->headers[$name] = is_array($value) ? $value : [$value];
        if (strtolower($name) === 'set-cookie') {
            $this->cookies = Cookie::makeFromArray($this->headers[$name]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value): self
    {
        $new = clone $this;
        return $new->setHeader($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value): self
    {
        return $this->withHeader(
            $name,
            array_merge($this->getHeader($name), $value)
        );
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name): self
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

    public function withHeaders(array $headers): self
    {
        $new = clone $this;
        return $new->setHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    public function setBody(StreamInterface $body): self
    {
        $this->stream = $body;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body): self
    {
        $new = clone $this;
        return $new->setBody($body);
    }
}
