<?php

namespace App\Models;

use App\Kernel\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'nickname', 'email', 'password'];
}
