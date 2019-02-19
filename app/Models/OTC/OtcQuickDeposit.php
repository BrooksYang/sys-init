<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcQuickDeposit extends Model
{
    /**
     * OTC快捷充值
     *
     * @var string
     */
    protected $table = 'otc_quick_deposits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    const OTC_QUICK_DEPOSITS_PAGE_SIZE = 30;

    // 定义支付状态，1支付中，2支付成功，3支付失败 4支付过期
    const PROCESSING = 1;
    const SUCCESS = 2;
    const FAILED = 3;
    const EXPIRED = 4;

    const STATUS = [
        self::PROCESSING => ['name' => '支付中',  'class' => 'info'],
        self::SUCCESS    => ['name' => '支付成功', 'class' => 'success'],
        self::FAILED     => ['name' => '支付失败', 'class' => 'warning'],
        self::EXPIRED    => ['name' => '支付过期', 'class' => 'default'],
    ];


    // 定义支付类型，1支付宝，2微信
    const ALIPAY = 1;
    const WECHAT = 2;

    const TYPE = [
        self::ALIPAY => ['name' => '支付宝', 'class' => 'iconfont icon-alipay'],
        self::WECHAT => ['name' => '微信',   'class' => 'iconfont icon-wechat'],
    ];


}
