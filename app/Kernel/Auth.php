<?php

namespace App\Kernel;

use App\Facades\Crypt;
use App\Facades\Hash;
use App\Facades\Route;
use App\Facades\Validator;
use App\Models\User;

class Auth
{
    // Auth::guard('guardName');
    public static $user = false;
    public static $viaRemember = false;

    public function register($user)
    {
        $v = $this->validatorRegister($user);
        if ($v->failed()) {
            return $v->getErrors();
        }
        $user['password'] = Hash::make($user['password']);
        self::$user = User::create($user) ?? false;
        $this->updateLogin(self::$user);
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
            return !User::existUserByAccount([
                'username' => $data,
                'email' => $data
            ]);
        }, 'Duplicate username or email.')->validate();
    }

    public function login($user, $remember = false)
    {
        $v = $this->validatorLogin($user);
        if ($v->failed()) {
            return $v->getErrors();
        }
        return $this->attempt($user, $remember);
    }

    public function logout()
    {
        $this->clearUserDataFromStorage();
        if (self::$user && self::$user['remember_token']) {
            $this->updateRememberToken(self::$user);
        }
        self::$user = false;
    }

    public function attempt($user, $remember = false)
    {
        $result = $this->attemptUser($user);
        if (is_array($result)) {
            return $result;
        }
        self::$user = $result;
        $this->updateLogin(self::$user, $remember);
        return true;
    }

    protected function updateLogin($user, $remember = false)
    {
        $this->updateSession($user['id']);
        if ($remember) {
            $this->updateRememberToken($user);
            $this->queueRememberTokenCookie($user);
        }
    }

    protected function attemptUser($user)
    {
        $db_user = User::getUserByAccount($user);
        if (!$db_user) {
            return [[
                'name' => 'account',
                'msg' => sprintf('No "%s" users found', $user['account'])
            ]];
        }
        if (!Hash::check($user['password'], $db_user['password'])) {
            return [[
                'name' => 'password',
                'msg' => 'Account does not match the password.'
            ]];
        }
        unset($user['account'], $user['password']);
        foreach ($user as $key => $value) {
            if ($db_user[$key] !== $value) {
                return [[
                    'name' => $key,
                    'msg' => 'These credentials do not match our records.'
                ]];
            }
        }
        return $db_user;
    }

    protected function validatorLogin(array $data)
    {
        return Validator::check($data, [
            'account' => 'required|string:1,255',
            'password' => 'required|string:8,255'
        ]);
    }

    protected function updateSession($id)
    {
        session()->put($this->getName(), $id);
        session()->regenerate();
    }

    protected function updateRememberToken($user)
    {
        $token = $user['remember_token'];
        if (empty($token)) {
            $token = str_random(40);
            User::updateToken($user['id'], $token);
            self::$user['remember_token'] = $token;
        }
    }

    protected function queueRememberTokenCookie($user)
    {
        cookie()->put([
            'name' => $this->getRememeberName(),
            'value' => $user['id'] . '|' . $user['remember_token'] . '|' . $user['password'],
            'expire' => time() + 60 * config('session.life_time'),
            'httponly' => true
        ]);
    }

    protected function clearUserDataFromStorage()
    {
        session()->forget($this->getName());
        if (cookie()->get($this->getRememeberName())) {
            cookie()->forget($this->getRememeberName());
        }
    }

    protected function getName()
    {
        return 'login_' . sha1(static::class);
    }

    protected function getRememeberName()
    {
        return 'remember_' . sha1(static::class);
    }

    public function check()
    {
        return $this->user() !== false;
    }

    public function guest()
    {
        return $this->user() === false;
    }

    public function user()
    {
        if (self::$user) {
            return self::$user;
        }
        // 检查Session
        $id = session()->get($this->getName());
        if (!is_null($id)) {
            self::$user = User::getUserById($id) ?? false;
        }
        // 检查Cookie
        if (!self::$user) {
            $remember_token = cookie()->get($this->getRememeberName());
            if (!is_null($remember_token)) {
                self::$user = User::getUserByToken($remember_token) ?? false;
                if (self::$user) {
                    $this->updateSession(self::$user['id']);
                    self::$viaRemember = true;
                }
            }
        }
        return self::$user;
    }

    public function viaRemember()
    {
        return self::$viaRemember;
    }

    public function routes()
    {
        // TODO: 找回密码
        Route::get('/login', 'Auth\LoginController@loginForm')->middleware('guest');
        Route::post('/login', 'Auth\LoginController@login')->middleware('guest');
        Route::any('/logout', 'Auth\LoginController@logout')->middleware('auth');

        Route::get('/register', 'Auth\RegisterController@registerForm')->middleware('guest');
        Route::post('/register', 'Auth\RegisterController@register')->middleware('guest');
    }
}
