<?php

namespace App\Controllers\Auth;

use App\Facades\Auth;
use App\Facades\Validator;
use App\Http\Request;
use App\Kernel\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $logged = Auth::login(
            [
                'account' => $request->input('account'),
                'password' => $request->input('password'),
            ],
            $request->input('remember_me') === 'on'
        );
        if ($logged === true) {
            return redirect('/home');
        }
        return view('auth/login', [
            'errors' => Validator::convertViewErrors($logged),
        ]);
    }

    public function loginForm()
    {
        return view('auth/login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
