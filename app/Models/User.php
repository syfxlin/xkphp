<?php

namespace App\Models;

use App\Kernel\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'nickname', 'email', 'password'];

    public static function getUserByAccount($user)
    {
        return User::where(
            'username',
            $user['account'] ?? $user['username']
        )->orWhere(
            'email',
            $user['account'] ?? $user['email']
        )->first();
    }

    public static function existUserByAccount($user)
    {
        return self::getUserByAccount($user) !== null;
    }

    public static function getUserByToken($remember_token)
    {
        list($id, $token, $password_hash) = explode('|', $remember_token);
        $db_user = User::where([
            'id' => $id,
            'remember_token' => $token,
            'password' => $password_hash
        ])->first();
        return $db_user ?? false;
    }

    public static function getUserById($id)
    {
        $db_user = User::where('id', $id)->first();
        return $db_user ?? false;
    }

    public static function updateToken($id, $token)
    {
        return User::where('id', $id)->update([
            'remember_token' => $token
        ]);
    }
}
