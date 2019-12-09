<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    /**
     * @var string
     */
    protected $table = 'auth_admins';

    protected $guarded = [];

}
