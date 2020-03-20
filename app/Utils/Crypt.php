<?php

namespace App\Utils;

class Crypt
{
    protected $key;
    protected $cipher;

    public function __construct(string $key, $cipher = 'AES-256-CBC')
    {
        if ($this->supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new \RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    public function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) ||
            ($cipher === 'AES-256-CBC' && $length === 32);
    }

    public function encrypt($value, $serialize = false)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher,
            $this->key,
            0,
            $iv
        );

        if ($value === false) {
            throw new \RuntimeException('Could not encrypt the data.');
        }
        $iv = base64_encode($iv);
        $mac = hash_hmac('sha256', $iv . $value, $this->key);
        $json = json_encode(compact('iv', 'value', 'mac'), JSON_UNESCAPED_SLASHES);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Could not encrypt the data.');
        }
        return base64_encode($json);
    }

    public function decrypt($payload, $unserialize = false)
    {
        $payload = $payload = json_decode(base64_decode($payload), true);
        if (!$this->vaild($payload)) {
            return;
        }
        $iv = base64_decode($payload['iv']);
        $decrypted = \openssl_decrypt(
            $payload['value'],
            $this->cipher,
            $this->key,
            0,
            $iv
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    public function vaild($payload)
    {
        if (
            !is_array($payload) || !isset($payload['iv'], $payload['value'], $payload['mac']) ||
            strlen(base64_decode($payload['iv'], true)) !== openssl_cipher_iv_length($this->cipher)
        ) {
            throw new \RuntimeException('The payload is invalid.');
        }
        if (!hash_equals(
            $payload['mac'],
            hash_hmac('sha256', $payload['iv'] . $payload['value'], $this->key)
        )) {
            throw new \RuntimeException('The MAC is invalid.');
        }
        return true;
    }

    public function encryptSerialize($value)
    {
        return $this->encrypt($value, true);
    }

    public function decryptSerialize($value)
    {
        return $this->decrypt($value, true);
    }

    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }

    public function decryptString($value)
    {
        return $this->decrypt($value, false);
    }
}
