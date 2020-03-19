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

    public function check($value, $hashed_value)
    {
        return password_verify($value, $hashed_value);
    }

    public function needsRehash($hashed_value, $algo = null, $options = null)
    {
        return password_needs_rehash($hashed_value, $algo ?? $this->algo, $options ?? $this->options);
    }
}
