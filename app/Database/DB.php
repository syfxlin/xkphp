<?php

namespace App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class DB extends Capsule
{
    public function __construct()
    {
        parent::__construct();
        $this->addConnection(config('database'));
        $this->setEventDispatcher(new Dispatcher(new Container()));
        $this->setAsGlobal();
        $this->bootEloquent();
    }
}
