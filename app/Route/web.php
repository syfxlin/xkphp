<?php

use App\Facades\Auth;
use App\Facades\Route;

Route::get('/home', 'HomeController@index')->middleware('auth');

Route::get('/users', function ($request) {
    return Auth::user();
});

Auth::routes();
