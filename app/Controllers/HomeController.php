<?php

namespace App\Controllers;

use App\Kernel\Request;

class HomeController
{
    public function index(Request $request)
    {
        return response(session('test', 123))->header('X-Test', '1');
    }
}
