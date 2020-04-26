<?php

namespace App\Exceptions;

use App\Http\Request;
use App\Http\Response;
use RuntimeException;
use function response;
use function view;

class Exception extends RuntimeException
{
    /**
     * @param Request $request
     * @return Response
     */
    public function render($request): Response
    {
        $message =
            $this->getMessage() === ''
                ? Response::$phrases[500]
                : $this->getMessage();
        $content = [
            'status' => 500,
            'message' => $message,
            'errors' => $message
        ];
        if ($request === null || !$request->ajax()) {
            $content = view('errors/errors', $content);
        }
        return response($content, 500);
    }
}
