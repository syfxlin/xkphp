<?php

namespace App\Http;

use App\Exceptions\Http\GetTellFailException;
use App\Exceptions\Http\InvalidStreamReferenceException;
use App\Exceptions\Http\ResourceMissException;
use App\Exceptions\Http\ResourceReadFailException;
use App\Exceptions\Http\ResourceWriteFailException;
use App\Exceptions\Http\SeekFailException;
use Psr\Http\Message\StreamInterface;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function is_resource;
use function is_string;
use function stream_get_contents;
use function stream_get_meta_data;
use function strpos;

class Stream implements StreamInterface
{
    /**
     * @var resource|null
     */
    private $resource;

    /**
     * @var string|resource
     */
    private $stream;

    /**
     * @var string
     */
    private $mode;

    public function __construct($stream, string $mode = 'rb')
    {
        $this->setResource($stream, $mode);
        $this->stream = $stream;
        $this->mode = $mode;
    }

    private function setResource($stream, $mode): void
    {
        $resource = null;
        if (is_string($stream)) {
            $resource = fopen($stream, $mode);
            if (!is_resource($resource)) {
                throw new InvalidStreamReferenceException(
                    'Invalid stream reference provided'
                );
            }
        }
        $this->resource = $resource;
    }

    public static function make(string $content, $stream, $mode = 'rb'): Stream
    {
        $stream = new static($stream, $mode);
        $stream->setContents($content);
        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->resource) {
            fclose($this->detach());
        }
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if ($this->resource === null) {
            return null;
        }
        $stats = fstat($this->resource);
        if ($stats !== false) {
            return $stats['size'];
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        $this->checkResource();
        $result = ftell($this->resource);
        if ($result === false) {
            throw new GetTellFailException('Get tell is fail');
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        if ($this->resource === null) {
            return true;
        }
        return feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        if ($this->resource === null) {
            return false;
        }
        return stream_get_meta_data($this->resource)['seekable'];
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->checkResource();
        $result = fseek($this->resource, $offset, $whence);
        if ($result === -1) {
            throw new SeekFailException('Seek fail');
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * @inheritDoc
     */
    public function isWritable(): bool
    {
        if ($this->resource === null) {
            return false;
        }
        $mode = stream_get_meta_data($this->resource)['mode'];
        return strpos($mode, 'x') !== false ||
            strpos($mode, 'w') !== false ||
            strpos($mode, 'c') !== false ||
            strpos($mode, 'a') !== false ||
            strpos($mode, '+') !== false;
    }

    /**
     * @inheritDoc
     */
    public function write($string): int
    {
        $this->checkResource();
        if (!$this->isWritable()) {
            throw new ResourceWriteFailException('Resources is not writable');
        }
        $result = fwrite($this->resource, $string);
        if ($result === false) {
            throw new ResourceWriteFailException('Resource write failed');
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function isReadable(): bool
    {
        if ($this->resource === null) {
            return false;
        }
        $mode = stream_get_meta_data($this->resource)['mode'];
        return strpos($mode, 'r') !== false || strpos($mode, '+') !== false;
    }

    /**
     * @inheritDoc
     */
    public function read($length): string
    {
        $this->checkResource();
        if (!$this->isReadable()) {
            throw new ResourceReadFailException('Resource is not readable');
        }
        $result = fread($this->resource, $length);
        if ($result === false) {
            throw new ResourceReadFailException('Resource read failed');
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        $this->checkResource();
        if (!$this->isReadable()) {
            throw new ResourceReadFailException('Resource is not readable');
        }
        $this->rewind();
        $result = stream_get_contents($this->resource);
        if ($result === false) {
            throw new ResourceReadFailException('Resource read failed');
        }
        return $result;
    }

    public function setContents(string $content): Stream
    {
        $this->close();
        $this->setResource($this->stream, $this->mode);
        $this->write($content);
        $this->rewind();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        if ($key === null) {
            return stream_get_meta_data($this->resource);
        }
        return stream_get_meta_data($this->resource)[$key] ?? null;
    }

    private function checkResource(): void
    {
        if ($this->resource === null) {
            throw new ResourceMissException('Resource is miss');
        }
    }
}
