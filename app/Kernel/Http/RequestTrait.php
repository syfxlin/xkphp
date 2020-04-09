<?php


namespace App\Kernel\Http;

use Psr\Http\Message\UriInterface;
use RuntimeException;

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

    /**
     * @inheritDoc
     */
    public function withRequestTarget($request_target)
    {
        $new = clone $this;
        $new->request_target = $request_target;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    private function setMethod($method): void
    {
        if (!in_array($method, [
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'CONNECT',
            'OPTIONS',
            'TRACE',
            'PATCH'
        ])) {
            throw new RuntimeException("Unsupported HTTP method \"$method\" provided");
        }
        $this->method = $method;
    }

    /**
     * @inheritDoc
     */
    public function withMethod($method)
    {
        $new = clone $this;
        $new->setMethod($method);
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritDoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $new = clone $this;
        $new->uri = $uri;
        if (($preserveHost && $this->hasHeader('Host')) || !$uri->getHost()) {
            return $new;
        }
        $host = $uri->getHost() . ($uri->getPort() ? ':' . $uri->getPath() : '');
        $new->headerAlias['host'] = 'Host';
        $new->headers['Host'] = [$host];
        return $new;
    }
}
