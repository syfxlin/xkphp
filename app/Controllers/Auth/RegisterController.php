<?php

namespace App\Controllers\Auth;

use App\Facades\Auth;
use App\Facades\Validator;
use App\Kernel\Controller;
use App\Kernel\Http\Request;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $result = Auth::register([
            'username' => $request->input('username'),
            'nickname' => $request->input('nickname'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'password_confirmed' => $request->input('password_confirmed'),
        ]);
        if ($result === true) {
            return redirect('/home');
        }
        return view('auth/register', [
            'errors' => Validator::convertViewErrors($result)
        ]);
    }

    public function registerForm()
    {
        return view('auth/register');
    }
}
