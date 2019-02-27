<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * 国家信息表
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * 批量赋值字段
     *
     * @var array
     */
    protected $guarded = [];

}
