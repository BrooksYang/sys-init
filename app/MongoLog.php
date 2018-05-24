<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MongoLog extends Eloquent
{
    //基本字段信息
    protected $connection = 'mongodb';
    protected $collection = 'admin';

    protected $dates = ['datetime'];
    //uid /email / type / ip / session / refer / agent / datatime/ app / level / route / method / parameter
    // [message] / [context] / [extra]
    protected $fillable = ['uid','email','type','ip','session','refer','agent','datetime', 'app', 'level',
        'route','method', 'parameter', 'message', 'context', 'extra'];
}
