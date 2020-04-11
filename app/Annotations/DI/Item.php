<?php

namespace App\Annotations\DI;

/**
 * Class Item
 * @package App\Annotations\DI
 * @Annotations
 */
final class Item
{
    /**
     * @var string
     * @Required
     */
    public $name;

    /**
     * @var string
     * @Required
     */
    public $value;
}
