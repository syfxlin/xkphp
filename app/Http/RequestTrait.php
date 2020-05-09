<?php

namespace App\Http;

use App\Exceptions\Http\MethodNotAllowedException;
use Psr\Http\Message\UriInterface;
use function in_array;

trait RequestTrait
{
    use MessageTrait;

    /**
     * @var string
     */
    private $method = 'GET';

    /**
     * @var string|null
     */
    private $request_target;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @inheritDoc
     */
    public function getRequestTarget(): string
    {
        if ($this->request_target !== null) {
            return $this->request_target;
        }
        $target = $this->uri->getPath();
        $target .= $this->uri->getQuery() ? '?' . $this->uri->getQuery() : '';
        return empty($target) ? '/' : $target;
    }

    public function setRequestTarget($request_target): self
    {
        $this->request_target = $request_target;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withRequestTarget($request_target): self
    {
        $new = clone $this;
        return $new->setRequestTarget($request_target);
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod($method): self
    {
        if (
            !in_array($method, [
                'GET',
                'HEAD',
                'POST',
                'PUT',
                'DELETE',
                'CONNECT',
                'OPTIONS',
                'TRACE',
                'PATCH',
            ])
        ) {
            throw new MethodNotAllowedException(
                "Unsupported HTTP method \"$method\" provided"
            );
        }
        $this->method = $method;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method): self
    {
        $new = clone $this;
        return $new->setMethod($method);
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function setUri(UriInterface $uri, $preserveHost = false): self
    {
        $this->uri = $uri;
        if (($preserveHost && $this->hasHeader('Host')) || !$uri->getHost()) {
            return $this;
        }
        $host =
            $uri->getHost() . ($uri->getPort() ? ':' . $uri->getPath() : '');
        $this->headerAlias['host'] = 'Host';
        $this->headers['Host'] = [$host];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        $new = clone $this;
        return $new->setUri($uri, $preserveHost);
    }
}
