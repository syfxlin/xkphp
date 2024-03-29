<?php

namespace App\Exceptions;

use App\Http\Request;
use App\Http\Response;
use Throwable;

class HttpException extends Exception
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response, Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct(
            $response->getContent(),
            $response->getStatus(),
            $previous
        );
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function render($request): Response
    {
        return $this->getResponse();
    }
}
