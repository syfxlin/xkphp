<?php

namespace App\Controllers;

use App\Facades\Storage;
use App\Kernel\Request;

class HomeController
{
    public function index(Request $request)
    {
        return view('home');
    }
}
