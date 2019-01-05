<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class UserDepositOrder extends Model
{
    // 订单状态：1 处理中 2 成功 3 失败 4 退回处理中 5 已退回 6 退回失败',
    const PROCESSING            = 1;
    const SUCCESS               = 2;
    const FAILED                = 3;
    const RETURN_PROCESSING     = 4;
    const RETURNED              = 5;
    const RETURN_FAILED         = 6;


    /**
     * 用户充值订单表
     *
     * @var string
     */
    protected $table = 'dcuex_user_deposit_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
