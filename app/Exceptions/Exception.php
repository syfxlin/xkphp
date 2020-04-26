<?php

namespace App\Exceptions;

use App\Http\Response;
use RuntimeException;

abstract class Exception extends RuntimeException
{
    abstract public function render(): Response;
}
