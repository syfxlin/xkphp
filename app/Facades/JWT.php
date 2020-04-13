<?php

namespace App\Facades;

use function base64_decode;
use function env;

/**
 * Class JWT
 * @package App\Facades
 *
 * @method static bool supported(string $key, string $algo)
 * @method static string sign(string $token, bool $raw = false, string $key = null, string $algo = null)
 * @method static bool verify(string $token, string $sign, bool $raw = false, string $key = null, string $algo = null)
 * @method static string encode(array $payload, int $exp = 86400, string $type = 'JWT', string $key = null, string $algo = null)
 * @method static mixed decode(string $token, string $key = null, string $algo = null)
 *
 * @see \App\Utils\JWT
 */
class JWT extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return \App\Utils\JWT::class;
    }

    public static function getArgs(): array
    {
        return [
            'key' => base64_decode(env('APP_KEY')),
            'cipher' => env('APP_JWT', 'HS256')
        ];
    }
}
