<?php

namespace App\Database;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;

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
