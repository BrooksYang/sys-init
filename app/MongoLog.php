<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MongoLog extends Model
{
    //基本字段信息
    protected $connection = 'mysql';
    protected $table = 'backend';
    //protected $collection = 'backend';

    protected $dates = ['datetime'];
    public $timestamps = false;

    //uid /email / type / ip / session / refer / agent / datetime/ app / level / route / method / parameter
    //[message] / [context] / [extra]
    protected $fillable = ['uid','email','type','ip','session','refer','agent','datetime', 'app', 'level',
        'route','method', 'parameter', 'message', 'context', 'extra'];
}
