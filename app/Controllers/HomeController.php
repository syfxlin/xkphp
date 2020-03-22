<?php

namespace App\Controllers;

use App\Kernel\Request;

class HomeController
{
    public function index(Request $request)
    {
        // return view('group.home', ['title' => print_r(FacadesRequest::all(), true)])->with('content', print_r(DB::select('SELECT 1'), true));
        // return session('test');
        // return "Hello";
        // return Storage::deleteDirectory('C:/Users/syfxl/Desktop/dir1');
        // return Crypt::encrypt('123');
        // config(['session.test' => true]);
        // return config('session');
        // return $request->session()->token();
        return view('home');
    }
}
