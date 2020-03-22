<?php

namespace App\Controllers\Auth;

use App\Facades\Auth;
use App\Facades\Validator;
use App\Kernel\Request;
use App\Kernel\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $logged = Auth::login([
            'account' => $request->input('account'),
            'password' => $request->input('password')
        ]);
        if ($logged === true) {
            redirect('/home');
        }
        return view('auth/login', [
            'errors' => Validator::convertViewErrors($logged)
        ]);
    }

    public function loginForm()
    {
        return view('auth/login');
    }

    public function logout()
    {
        Auth::logout();
        redirect('/login');
    }
}
