<?php

namespace App\Models;

use App\Kernel\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'nickname', 'email', 'password'];

    /**
     * 获取用户对象，通过 account 字段
     *
     * @param   mixed   $user  用户数组或对象
     *
     * @return  User|null User 对象
     */
    public static function getUserByAccount($user): ?User
    {
        return self::where('username', $user['account'] ?? $user['username'])
            ->orWhere('email', $user['account'] ?? $user['email'])
            ->first();
    }

    /**
     * 查看指定用户是否存在
     *
     * @param   mixed   $user  用户对象或数组
     *
     * @return  bool           是否存在
     */
    public static function existUserByAccount($user): bool
    {
        return self::getUserByAccount($user) !== null;
    }

    /**
     * 通过 remember token 获取用户对象
     *
     * @param   string  $remember_token  remember token
     *
     * @return  User|null     User 对象
     */
    public static function getUserByToken(string $remember_token): ?User
    {
        [$id, $token, $password_hash] = explode('|', $remember_token);
        return self::where([
            'id' => $id,
            'remember_token' => $token,
            'password' => $password_hash
        ])->first();
    }

    /**
     * 通过 id 获取用户对象
     *
     * @param   int  $id  用户 id
     *
     * @return  User|null     User 对象
     */
    public static function getUserById(int $id): ?User
    {
        return self::where('id', $id)->first();
    }

    /**
     * 更新 remember token
     *
     * @param   int     $id     用户 id
     * @param   string  $token  remember token
     *
     * @return  User|null     User 对象
     */
    public static function updateToken(int $id, string $token): ?User
    {
        return self::where('id', $id)->update([
            'remember_token' => $token
        ]);
    }
}
