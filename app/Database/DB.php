<?php

namespace App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class DB extends Capsule
{
    public function __construct()
    {
        $capsule = new Capsule();

        $capsule->addConnection(require_once __DIR__ . "/../../config/database.php");

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
