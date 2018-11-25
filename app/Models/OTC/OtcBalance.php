<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcBalance extends Model
{
    /**
     * 钱包余额表
     *
     * @var string
     */
    protected $table = 'otc_balances';

    /**
     * 批量赋值字段
     *
     * @var array
     */
    protected $guarded = [];


}
