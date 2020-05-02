<?php

namespace App\Contracts;

use App\Http\Response;

interface Responseable
{
    public function toResponse(): Response;
}
