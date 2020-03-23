<?php

namespace App\Utils;

class Storage
{
    public function vaildPath(string $path)
    {
        $path = is_array($path) ? $path : [$path];
        foreach ($path as $value) {
            if (stripos(realpath($value), storage_path()) !== 0) {
                throw new \RuntimeException('Path is outside of the defined root');
            }
        }
    }

    public function __call($name, $arguments)
    {
        $two_args_fun = ['move', 'copy', 'link', 'moveDirectory', 'copyDirectory'];
        $arguments[0] = realpath(storage_path() . '/' . $arguments[0]);
        $this->vaildPath($arguments[0]);
        if (in_array($name, $two_args_fun)) {
            $arguments[1] = realpath(storage_path() . '/' . $arguments[1]);
            $this->vaildPath($arguments[1]);
        }
        $file = new File();
        return $file->$name(...$arguments);
    }
}
