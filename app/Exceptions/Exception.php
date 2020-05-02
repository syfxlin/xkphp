<?php

namespace App\Exceptions;

use App\Http\Request;
use App\Http\Response;
use RuntimeException;
use function response;
use function view;

class Exception extends RuntimeException implements \App\Contracts\Exception
{
    /**
     * @param Request $request
     * @return Response
     */
    public function render($request): Response
    {
        return $this->toResponse(
            $this->getCode() === 0 ? 500 : $this->getCode(),
            $this->getMessage(),
            $this->getMessage(),
            [],
            $request
        );
    }

    /**
     * @param int $status
     * @param string $message
     * @param string $errors
     * @param array $headers
     * @param Request $request
     * @return Response
     */
    protected function toResponse(
        int $status = 500,
        string $message = '',
        $errors = '',
        array $headers = [],
        $request = null
    ): Response {
        $message =
            $message === '' ? Response::$phrases[$status] : $this->getMessage();
        $content = [
            'status' => $status,
            'message' => $message,
            'errors' => $errors
        ];
        if ($request === null || !$request->ajax()) {
            $content = view('errors/errors', $content);
        }
        return response($content, $status, $headers);
    }
}
