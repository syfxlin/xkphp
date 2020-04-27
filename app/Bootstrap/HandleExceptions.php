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
    /**
     * @var string
     */
    protected $env;

    public function boot(): void
    {
        $this->env = $this->app->environment();
        if ($this->env !== 'development') {
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
        try {
            $request = $this->app->make(Request::class);
        } catch (\Exception $ex) {
            $request = null;
        }
        if (!$e instanceof Exception) {
            $e = new Exception(Response::$phrases[500], 500, $e->getPrevious());
        }
        return $e->render($request);
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
