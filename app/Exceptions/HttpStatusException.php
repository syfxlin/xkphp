<?php

namespace App\Exceptions;

use App\Http\Response;
use Throwable;

class HttpStatusException extends Exception
{
    /**
     * @var array
     */
    protected $headers;

    public function __construct(
        int $code = 500,
        string $message = '',
        array $headers = [],
        Throwable $previous = null
    ) {
        $this->headers = $headers;
        parent::__construct($message, $code, $previous);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function render($request): Response
    {
        return $this->toResponse(
            $this->getCode(),
            $this->getMessage(),
            $this->getMessage(),
            $this->getHeaders(),
            $request
        );
    }
}
