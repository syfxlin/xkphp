<?php

namespace App\Utils;

class Hash
{
    protected $options = [];
    protected $algo = PASSWORD_BCRYPT;

    public function make($value, $algo = null, $options = null)
    {
        $hash = password_hash($value, $algo ?? $this->algo, $options ?? $this->options);

        if ($hash === false) {
            throw new \RuntimeException('Algo hashing not supported.');
        }

        return $hash;
    }

    public function check($value, $hashedValue)
    {
        return password_verify($value, $hashedValue);
    }

    public function needsRehash($hashedValue, $algo = null, $options = null)
    {
        return password_needs_rehash($hashedValue, $algo ?? $this->algo, $options ?? $this->options);
    }
}
