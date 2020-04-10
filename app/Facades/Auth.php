<?php

namespace App\Facades;

use App\Models\User;

/**
 * Class Auth
 * @package App\Facades
 *
 * @method static array|bool login(array $user, bool $remember = false)
 * @method static void logout()
 * @method static array|bool register(array $user)
 * @method static array|bool attempt(array $user, bool $remember = false)
 * @method static bool check()
 * @method static bool guest()
 * @method static User|bool user()
 * @method static bool viaRemember()
 * @method static void routes()
 *
 * @see \App\Kernel\Auth
 */
class Auth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Kernel\Auth::class;
    }
}
