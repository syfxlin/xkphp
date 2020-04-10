<?php

namespace App\Facades;
/**
 * @method static bool supported(string $key, string $cipher)
 * @method static string encrypt($value, bool $serialize = false)
 * @method static mixed decrypt(string $payload, bool $unserialize = false)
 * @method static bool vaild($payload)
 * @method static string encryptSerialize($value)
 * @method static mixed decryptSerialize($value)
 * @method static string encryptString(string $value)
 * @method static string decryptString(string $value)
 *
 * @see \App\Utils\Crypt
 */
class Crypt extends Facade
{
    protected static $class = \App\Utils\Crypt::class;

    public static function getArgs(): array
    {
        return [
            base64_decode(env('APP_KEY')),
            env('APP_CIPHER', 'AES-256-CBC')
        ];
    }
}
