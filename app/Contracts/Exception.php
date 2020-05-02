<?php

namespace App\Contracts;

use App\Http\Response;

interface Exception
{
    public function render($request): Response;
}
