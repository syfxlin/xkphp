<?php

use App\Facades\Auth;
use App\Facades\Cookie;
use App\Facades\Response;
use App\Facades\Route;
use App\Kernel\Http\Request;

Auth::routes();

Route::get('/home', 'HomeController@index')->middleware('auth');

Route::get('/users', function (Request $request) {
    return Auth::user();
});

Route::get('/', function (Request $request) {
    return Response::text('123');
});
