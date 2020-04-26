<?php

namespace App\Exceptions;

use a;
use App\Http\Response;
use Throwable;

class HttpStatusException extends Exception
{
    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $headers;

    public function __construct(
        int $status = 500,
        string $message = '',
        array $headers = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->status = $status;
        $this->headers = $headers;
        parent::__construct($message, $code, $previous);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function render($request): Response
    {
        return $this->toResponse(
            $this->getStatus(),
            $this->getMessage(),
            $this->getMessage(),
            $this->getHeaders(),
            $request
        );
    }
}
