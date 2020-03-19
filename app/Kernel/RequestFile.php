<?php

namespace App\Kernel;

class RequestFile
{
    public $file;
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function isValid()
    {
        return $this->file['error'] === 0;
    }

    public function path()
    {
        return $this->file['tmp_name'];
    }

    public function name()
    {
        return $this->file['name'];
    }

    public function type()
    {
        return $this->file['type'];
    }

    public function store($path)
    {
        $this->storeAs($path, str_random(10) . "." . pathinfo($this->file['name'], PATHINFO_EXTENSION));
    }

    public function storeAs($path, $filename = null)
    {
        move_uploaded_file(
            $this->file['tmp_name'],
            $path . ($filename !== null ? $filename : $this->file['name'])
        );
    }
}
