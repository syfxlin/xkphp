<?php

namespace App\Facades;
/**
 * Class Crypt
 * @package App\Facades
 *
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
    protected static function getFacadeAccessor(): string
    {
        return \App\Utils\Crypt::class;
    }

    protected static function getArgs(): array
    {
        return [
            'key' => base64_decode(env('APP_KEY')),
            'cipher' => env('APP_CIPHER', 'AES-256-CBC')
        ];
    }
}
