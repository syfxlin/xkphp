<?php

namespace App\Exceptions;

use App\Http\Response;
use Throwable;

class HttpException extends Exception
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(
        Response $response,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function render(): Response
    {
        return $this->getResponse();
    }
}
