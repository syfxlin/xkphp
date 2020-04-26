<?php

namespace App\Bootstrap;

use App\Exceptions\Exception;
use App\Http\Request;
use App\Http\Response;
use ErrorException;
use Throwable;
use function in_array;
use function response;
use function view;

class HandleExceptions extends Bootstrap
{
    public function boot(): void
    {
        if (!$this->app->environment('testing')) {
            error_reporting(-1);
            set_error_handler([$this, 'handleError']);
            set_exception_handler([$this, 'handleException']);
            register_shutdown_function([$this, 'handleDown']);
            ini_set('display_errors', 'Off');
        }
    }

    public function handleError(
        $level,
        $message,
        $file = '',
        $line = 0,
        $context = []
    ): void {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleException(Throwable $e): void
    {
        $this->renderResponse($e)->send();
    }

    protected function renderResponse(Throwable $e): Response
    {
        $request = $this->app->make(Request::class);
        if ($e instanceof Exception) {
            return $e->render();
        }
        return $this->renderExceptionResponse($request, $e);
    }

    protected function renderExceptionResponse(
        Request $request,
        Throwable $e
    ): Response {
        $code = $e->getCode() === 0 ? 500 : $e->getCode();
        $message =
            $e->getMessage() === ''
                ? Response::$phrases[$code]
                : $e->getMessage();
        $content = [
            'status' => $code,
            'message' => $message,
            'errors' => $message
        ];
        if (!$request->ajax()) {
            $content = view('errors/errors', $content);
        }
        return response($content, $code);
    }

    public function handleDown(): void
    {
        $error = error_get_last();
        if (
            $error !== null &&
            in_array(
                $error['type'],
                [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE],
                true
            )
        ) {
            $this->handleException(
                new ErrorException(
                    $error['message'],
                    $error['type'],
                    0,
                    $error['file'],
                    $error['line']
                )
            );
        }
    }
}
