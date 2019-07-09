<?php

namespace App\Models\OTC;

use App\Models\Currency;
use App\Models\LegalCurrency;
use App\User;
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

    // 类型，1出售，2购买
    const SELL = 1;
    const BUY = 2;

    // 类型文本
    public static $typeText = [
        self::SELL => '出售',
        self::BUY  => '购买',
    ];

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


    // 申诉状态，1已申诉，2申诉处理中，3申诉完结
    const NOT_APPEAL = 0;
    const APPEALED   = 1;
    const APPEALING  = 2;
    const APPEAL_END = 3;

    // 申诉状态文本
    public static $appealText = [
        self::NOT_APPEAL => '无申诉',
        self::APPEALED   => '已申诉',
        self::APPEALING  => '申诉处理中',
        self::APPEAL_END => '申诉完结'
    ];

    /**
     * 广告所有者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tradeOwner()
    {
        return $this->belongsTo(User::class, 'from_user_id')
            ->select(['id', 'username', 'email', 'phone']);
    }

    /**
     * 订单所有者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select(['id', 'username', 'email', 'phone']);
    }

    /**
     * 获取该订单的币种信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')
            ->select(['id', 'currency_title_en_abbr as abbr']);
    }

    /**
     * 获取该订单的法币信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function legalCurrency()
    {
        return $this->belongsTo(LegalCurrency::class, 'legal_currency_id')
            ->select(['id', 'abbr']);
    }

}
