<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // 认证状态，1未认证，2待审核，3已认证，4认证失败
    const NOT_VERIFY = 1;
    const UNDER_VERIFY = 2;
    const VERIFIED = 3;
    const VERIFY_FAILED = 4;
}
