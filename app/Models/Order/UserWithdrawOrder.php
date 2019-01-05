<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class UserWithdrawOrder extends Model
{
    // 订单状态：-1 已撤销 1 等待受理 2 处理中 3 已发币 4 失败',
    const CANCELED    = -1;
    const WAITING     = 1;
    const PROCESSING  = 2;
    const RELEASED    = 3;
    const FAILED      = 4;


    /**
     * 用户提币订单表
     *
     * @var string
     */
    protected $table = 'dcuex_user_withdraw_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
