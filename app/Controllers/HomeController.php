<?php

namespace App\Controllers;

use App\Kernel\Request;

class HomeController
{
    public function index(Request $request)
    {
        return response($request->cookie('r', '123'))->header('X-Test', '1');
    }
}
