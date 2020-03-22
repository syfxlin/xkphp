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

Route::get('/home', 'HomeController@index')->middleware('auth');

Route::get('/users', function ($request) {
    // return Auth::register([
    //     'username' => 'syfxlin',
    //     'nickname' => 'Otstar Lin',
    //     'email' => 'syfxlin@gmail.com',
    //     'password' => '12345678',
    //     'password_confirmed' => '12345678'
    // ]);
    // return Auth::login(['account' => 'syfxlin@gmail.com', 'password' => '12345678'], true);
    return Auth::user();
    // return Auth::logout();
});

Auth::routes();
