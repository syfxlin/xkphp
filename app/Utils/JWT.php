<?php

namespace App\Utils;

use App\Exceptions\Utils\AlgoNotSupportException;
use App\Exceptions\Utils\InvalidPayloadException;
use App\Exceptions\Utils\InvalidSignException;
use App\Exceptions\Utils\InvalidTokenException;
use function array_key_exists;
use function array_merge;
use function array_pad;
use function base64_decode;
use function base64_encode;
use function explode;
use function gmdate;
use function hash_equals;
use function hash_hmac;
use function implode;
use function json_decode;
use function json_encode;
use function strlen;
use function time;

class JWT
{
    /**
     * Key
     *
     * @var string
     */
    protected $key;

    /**
     * 算法
     *
     * @var string
     */
    protected $algo;

    /**
     * 默认的 Payload
     *
     * @var array
     */
    protected $default_payload;

    protected static $supported = [
        'HS256' => 'SHA256',
        'HS384' => 'SHA384',
        'HS512' => 'SHA512',
    ];

    /**
     * 加密构造器
     *
     * @param string $key .env APP_KEY
     * @param string $algo
     * @param array $default_payload
     */
    public function __construct(
        string $key,
        string $algo = 'HS256',
        array $default_payload = []
    ) {
        if ($this->supported($key, $algo)) {
            $this->key = $key;
            $this->algo = self::$supported[$algo];
            $this->default_payload = $default_payload;
        } else {
            throw new AlgoNotSupportException(
                'The only supported algos are HS265,HS384,HS512 with the correct key lengths (key length >= 16).'
            );
        }
    }

    /**
     * 验证加密算法和 Key
     *
     * @param   string  $key     Key
     * @param   string  $algo  加密算法
     *
     * @return  bool             是否合法
     */
    public function supported(string $key, string $algo): bool
    {
        return strlen($key) >= 16 && array_key_exists($algo, self::$supported);
    }

    public function sign(
        string $token,
        bool $raw = false,
        string $key = null,
        string $algo = null
    ): string {
        $sign = hash_hmac(
            $algo ?? $this->algo,
            $token,
            $key ?? $this->key,
            true
        );
        return $raw ? $sign : base64_encode($sign);
    }

    public function verify(
        string $token,
        string $sign,
        bool $raw = false,
        string $key = null,
        string $algo = null
    ): bool {
        $token = (string) ($raw ? $token : base64_decode($token));
        return hash_equals($this->sign($token, $raw, $key, $algo), $sign);
    }

    public function encode(
        array $payload,
        int $exp = 86400,
        string $type = 'JWT',
        string $key = null,
        string $algo = null
    ): string {
        $timestamp = time();
        $header = [
            'alg' => $this->algo,
            'typ' => $type,
        ];
        $auto_payload = [
            'iat' => $timestamp,
            'nbf' => $timestamp,
            'exp' => $timestamp + $exp,
        ];
        $payload = array_merge($this->default_payload, $auto_payload, $payload);
        $segments = [];
        $segments[] = base64_encode(json_encode($header));
        $segments[] = base64_encode(json_encode($payload));
        $sign = $this->sign(implode('.', $segments), false, $key, $algo);
        $segments[] = $sign;
        return implode('.', $segments);
    }

    public function decode(
        string $token,
        string $key = null,
        string $algo = null
    ) {
        $timestamp = time();
        [$header, $payload, $sign] = array_pad(explode('.', $token), 3, null);
        $signed = "$header.$payload";
        if ($header === null || $payload === null || $sign === null) {
            throw new InvalidPayloadException('Error segments count');
        }
        if (($header = json_decode(base64_decode($header), true)) === null) {
            throw new InvalidPayloadException('Invalid header encoding');
        }
        if (($payload = json_decode(base64_decode($payload), true)) === null) {
            throw new InvalidPayloadException('Invalid payload encoding');
        }
        if (($sign = base64_decode($sign)) === false) {
            throw new InvalidPayloadException('Invalid sign encoding');
        }
        if (
            !isset($header['alg']) ||
            array_key_exists($header['alg'], self::$supported)
        ) {
            if ($algo === null) {
                throw new AlgoNotSupportException(
                    'Algorithm not supported or empty'
                );
            }
        }
        if (
            !$this->verify(
                $signed,
                $sign,
                true,
                $key ?? $this->key,
                $algo ?? $header['alg']
            )
        ) {
            throw new InvalidSignException('Signature verification failed');
        }
        if (isset($payload['nbf']) && $payload['nbf'] > $timestamp) {
            throw new InvalidTokenException(
                'The token is not yet valid [' .
                    gmdate('D, d M Y H:i:s T', $payload['nbf']) .
                    ']'
            );
        }
        if (isset($payload['iat']) && $payload['iat'] > $timestamp) {
            throw new InvalidTokenException(
                'The token is not yet valid [' .
                    gmdate('D, d M Y H:i:s T', $payload['iat']) .
                    ']'
            );
        }
        if (isset($payload['exp']) && $payload['exp'] < $timestamp) {
            throw new InvalidTokenException(
                'The token has expired [' .
                    gmdate('D, d M Y H:i:s T', $payload['exp']) .
                    ']'
            );
        }
        return $payload;
    }
}
