<?php

namespace App\Kernel;

use App\Facades\Hash;
use App\Facades\Validator;
use App\Models\User;

class Auth
{
    // public function login()
    public function register($user)
    {
        $v = $this->validatorRegister($user);
        if ($v->failed()) {
            // TODO: 错误处理
            return $v->getErrors();
        }
        $user['password'] = Hash::make($user['password']);
        User::create($user);
        return true;
    }

    protected function validatorRegister(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string:1,255|unique_users',
            'nickname' => 'required|string:1,255',
            'email' => 'required|string:3,255|email|unique_users',
            'password' => 'required|string:8,255',
            'password_confirmed' => 'required|string:8,255|eqField:password'
        ])->addValidator('unique_users', function ($data) {
            return User::where('username', $data)->orWhere('email', $data)->first() === null;
        }, 'Duplicate username or email.')->validate();
    }

    public function check($user)
    {
        $v = $this->validatorLogin($user);
        if ($v->failed()) {
            // TODO: 错误处理
            return $v->getErrors();
        }
        $db_user = User::where(
            'username',
            $user['account']
        )->orWhere(
            'email',
            $user['account']
        )->first();
        if (!$db_user) {
            // TODO: 错误处理
            return false;
        }
        if (Hash::check($user['password'], $db_user['password'])) {
            return $db_user['id'];
        } else {
            // TODO: 错误处理
            return false;
        }
    }

    protected function validatorLogin(array $data)
    {
        return Validator::check($data, [
            'username' => 'required|string:1,255|unique_users',
            'password' => 'required|string:8,255'
        ]);
    }
}
