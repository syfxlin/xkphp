<?php

namespace App\Contracts;

interface Listener
{
    /**
     * @param $event
     * @param array $args
     * @return void
     */
    public function handle($event, array $args = []): void;
}
