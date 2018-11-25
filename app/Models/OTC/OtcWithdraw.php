<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcWithdraw extends Model
{

    // 订单状态：1 等待受理 2 处理中 3 已发币 4 失败',
    const OTC_PENDING  = 2;
    const OTC_RELEASED = 3;

    /**
     * OTC提现订单
     *
     * @var string
     */
    protected $table = 'otc_withdraws';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];





}
