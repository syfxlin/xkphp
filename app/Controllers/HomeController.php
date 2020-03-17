<?php

namespace App\Controllers;

use App\Kernel\Request;

class HomeController
{
    public function index(Request $request)
    {
        return $request->all();
    }
}
