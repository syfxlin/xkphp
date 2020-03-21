<?php

use App\Facades\Auth;
use App\Facades\Route;
use App\Facades\Validator;

Route::view('/user', 'home', ['title' => 'test', 'content' => 'content']);

Route::prefix('/group')->middleware('test')->group(function () {
    Route::get('/users', function () {
        return "Users Page1";
    });
    Route::redirect('/user', '/group/users', 302);
});

Route::any('/home', 'HomeController@index');

Route::get('/users', function ($request) {
    // $auth = new Auth();
    return Auth::register([
        'username' => 'syfxlin',
        'nickname' => 'Otstar Lin',
        'email' => 'syfxlin@gmail.com',
        'password' => '123456',
        'password_confirmed' => '123456'
    ]);
    // return Auth::check(['account' => 'syfxlin@gmail.com', 'password' => '123456']);
});
