<?php

namespace App\Kernel;

use App\Facades\Hash;
use App\Facades\Route;
use App\Facades\Validator;
use App\Http\Cookie;
use App\Models\User;

class Auth
{
    // Auth::guard('guardName');

    /**
     * 已登录的用户
     *
     * @var User|false
     */
    public static $user = false;

    /**
     * 是否是通过 Remember Token 登录的
     *
     * @var bool
     */
    public static $viaRemember = false;

    /**
     * 注册
     *
     * @param   array   $user  用户信息
     *
     * @return  array|bool     错误列表或成功
     */
    public function register(array $user)
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

    /**
     * 验证注册用户信息有效性
     *
     * @param   array  $data  用户信息
     *
     * @return  mixed
     */
    protected function validatorRegister(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string:1,255|unique_users',
            'nickname' => 'required|string:1,255',
            'email' => 'required|string:3,255|email|unique_users',
            'password' => 'required|string:8,255',
            'password_confirmed' => 'required|string:8,255|eqField:password'
        ])
            ->addValidator(
                'unique_users',
                function ($data) {
                    return !User::existUserByAccount([
                        'username' => $data,
                        'email' => $data
                    ]);
                },
                'Duplicate username or email.'
            )
            ->validate();
    }

    /**
     * 登录
     *
     * @param   mixed   $user      用户信息
     * @param   bool    $remember  是否记住
     *
     * @return  array|bool         错误列表或成功
     */
    public function login(array $user, bool $remember = false)
    {
        $v = $this->validatorLogin($user);
        if ($v->failed()) {
            return $v->getErrors();
        }
        return $this->attempt($user, $remember);
    }

    /**
     * 登出
     *
     * @return  void
     */
    public function logout(): void
    {
        $this->clearUserDataFromStorage();
        if (self::$user && self::$user['remember_token']) {
            $this->updateRememberToken(self::$user);
        }
        self::$user = false;
    }

    /**
     * 手动登录
     *
     * @param   array  $user      用户信息
     * @param   bool   $remember  是否记住
     *
     * @return  array|bool
     */
    public function attempt(array $user, bool $remember = false)
    {
        $result = $this->attemptUser($user);
        if (is_array($result)) {
            return $result;
        }
        self::$user = $result;
        $this->updateLogin(self::$user, $remember);
        return true;
    }

    /**
     * 更新登录信息
     *
     * @param   User  $user      用户对象
     * @param   bool  $remember  是否记住
     *
     * @return  void
     */
    protected function updateLogin(User $user, bool $remember = false): void
    {
        $this->updateSession($user['id']);
        if ($remember) {
            $this->updateRememberToken($user);
            $this->queueRememberTokenCookie($user);
        }
    }

    protected function attemptUser(array $user)
    {
        $db_user = User::getUserByAccount($user);
        if (!$db_user) {
            return [
                [
                    'name' => 'account',
                    'msg' => sprintf('No "%s" users found', $user['account'])
                ]
            ];
        }
        if (!Hash::check($user['password'], $db_user['password'])) {
            return [
                [
                    'name' => 'password',
                    'msg' => 'Account does not match the password.'
                ]
            ];
        }
        unset($user['account'], $user['password']);
        foreach ($user as $key => $value) {
            if ($db_user[$key] !== $value) {
                return [
                    [
                        'name' => $key,
                        'msg' => 'These credentials do not match our records.'
                    ]
                ];
            }
        }
        return $db_user;
    }

    /**
     * 验证登录信息有效性
     *
     * @param   array  $data  登录信息
     *
     * @return  mixed
     */
    protected function validatorLogin(array $data)
    {
        return Validator::check($data, [
            'account' => 'required|string:1,255',
            'password' => 'required|string:8,255'
        ]);
    }

    /**
     * 更新 Session 信息
     *
     * @param   int  $id  用户 ID
     *
     * @return  void
     */
    protected function updateSession(int $id): void
    {
        session()->put($this->getName(), $id);
        session()->regenerate();
    }

    /**
     * 更新 remember token
     *
     * @param   User  $user  用户对象
     *
     * @return  void
     */
    protected function updateRememberToken(User $user): void
    {
        $token = $user['remember_token'];
        if (empty($token)) {
            $token = str_random(40);
            User::updateToken($user['id'], $token);
            self::$user['remember_token'] = $token;
        }
    }

    /**
     * 更新 remember token cookie
     *
     * @param   User  $user  用户对象
     *
     * @return  void
     */
    protected function queueRememberTokenCookie(User $user): void
    {
        cookie()->put(
            Cookie::make(
                $this->getRememeberName(),
                $user['id'] .
                    '|' .
                    $user['remember_token'] .
                    '|' .
                    $user['password']
            )
                ->withMaxAge(60 * config('session.cookie_lifetime'))
                ->withHttpOnly(true)
        );
    }

    /**
     * 清除用户状态
     *
     * @return  void
     */
    protected function clearUserDataFromStorage(): void
    {
        session()->forget($this->getName());
        if (cookie()->get($this->getRememeberName())) {
            cookie()->forget($this->getRememeberName());
        }
    }

    /**
     * 获取 Login session 名称
     *
     * @return  string
     */
    protected function getName(): string
    {
        return 'login_' . sha1(static::class);
    }

    /**
     * 获取 remember token session 名称
     *
     * @return  string
     */
    protected function getRememeberName(): string
    {
        return 'remember_' . sha1(static::class);
    }

    /**
     * 检查是否登录
     *
     * @return  bool
     */
    public function check(): bool
    {
        return $this->user() !== false;
    }

    /**
     * 检查是否未登录
     *
     * @return  bool
     */
    public function guest(): bool
    {
        return $this->user() === false;
    }

    /**
     * 获取已登录的用户，若未登录则返回false
     *
     * @return  User|bool
     */
    public function user()
    {
        if (self::$user) {
            return self::$user;
        }
        // 检查Session
        $id = session()->get($this->getName());
        if ($id !== null) {
            self::$user = User::getUserById($id) ?? false;
        }
        // 检查Cookie
        if (!self::$user) {
            $remember_token = cookie()->get($this->getRememeberName());
            if ($remember_token !== null && $remember_token !== '') {
                self::$user = User::getUserByToken($remember_token) ?? false;
                if (self::$user) {
                    $this->updateSession(self::$user['id']);
                    self::$viaRemember = true;
                }
            }
        }
        return self::$user;
    }

    /**
     * 获取是否通过 remember token 登录
     *
     * @return  bool
     */
    public function viaRemember(): bool
    {
        return self::$viaRemember;
    }

    /**
     * 路由注入
     *
     * @return  void
     */
    public function routes(): void
    {
        // TODO: 找回密码
        Route::get('/login', 'Auth\LoginController@loginForm')->middleware(
            'guest'
        );
        Route::post('/login', 'Auth\LoginController@login')->middleware(
            'guest'
        );
        Route::any('/logout', 'Auth\LoginController@logout')->middleware(
            'auth'
        );

        Route::get(
            '/register',
            'Auth\RegisterController@registerForm'
        )->middleware('guest');
        Route::post(
            '/register',
            'Auth\RegisterController@register'
        )->middleware('guest');
    }
}
