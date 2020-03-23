<?php

namespace Migration;

class CreateTableUsers extends Migration
{
    public function up()
    {
        $this->create('test', ['id' => 'int']);
    }

    public function drop()
    {
        $this->delete('test');
    }
}
