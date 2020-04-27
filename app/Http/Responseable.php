<?php

namespace App\Http;

interface Responseable
{
    public function toResponse(): Response;
}
