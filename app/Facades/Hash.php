<?php

namespace App\Facades;

/**
 * @method static string make(string $value, $algo = null, array $options = null)
 * @method static bool check(string $value, string $hashed_value)
 * @method static bool needsRehash(string $hashed_value, $algo = null, array $options = null)
 *
 * @see \App\Utils\Hash
 */
class Hash extends Facade
{
    protected static $class = \App\Utils\Hash::class;
}
