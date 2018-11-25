<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcOrder extends Model
{

    /**
     * OTC钱包余额表
     *
     * @var string
     */
    protected $table = 'otc_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 定义状态，1已下单，2已支付，3确认收款(已发币)，4确认收币，5已取消
    const ORDERED = 1;
    const PAID = 2;
    const RELEASED = 3;
    const RECEIVED = 4;
    const CANCELED = 5;

    // 状态文本
    public static $statusTexts = [
        self::ORDERED  => '已下单',
        self::PAID     => '已支付',
        self::RELEASED => '已发币',
        self::RECEIVED => '已完成',
        self::CANCELED => '已取消',
    ];



}
