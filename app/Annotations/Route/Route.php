<?php

namespace App\Annotations\Route;

/**
 * Class Route
 * @package App\Annotations\Route
 * @Annotations
 */
final class Route
{
    /**
     * @Required
     */
    public $value;

    /**
     * @var array
     */
    public $method;
}
