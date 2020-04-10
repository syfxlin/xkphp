<?php

namespace App\Controllers;

use App\Http\Request;

class HomeController
{
    public function index(Request $request)
    {
        return view('home');
    }
}
