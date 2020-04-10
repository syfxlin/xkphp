<?php

namespace Migration;

class CreateTableUsers extends Migration
{
    public function up(): void
    {
        $this->create('test', ['id' => 'int']);
    }

    public function drop(): void
    {
        $this->delete('test');
    }
}
