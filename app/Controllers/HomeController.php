<?php

namespace App\Controllers;

use App\Kernel\Request;

class HomeController
{
    public function index(Request $request)
    {
        return view('group.home', ['title' => 'title'])->with('content', 'content');
    }
}
