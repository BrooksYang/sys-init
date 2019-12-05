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
    // 定义状态，1已下单，2已支付，3已发币，4已完成，5已取消
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

    // 申诉状态，1已申诉，2申诉处理中，3申诉完结, 4已撤诉
    const NOT_APPEAL      = 0;
    const APPEALED        = 1;
    const APPEALING       = 2;
    const APPEAL_END      = 3;
    const APPEAL_CANCELED = 4;

    // 申诉状态文本
    public static $appealText = [
        self::NOT_APPEAL      => '无申诉',
        self::APPEALED        => '已申诉',
        self::APPEALING       => '申诉处理中',
        self::APPEAL_END      => '申诉完结',
        self::APPEAL_CANCELED => '已撤诉'
    ];

    // 分组统计文本
    const GROUP =  [
        'day'   => ['name'=>'按日'],
        'week'  => ['name'=>'按周'],
        'month' => ['name'=>'按月']
    ];

    // 订单是否由领导人兜底完成，0否，1是
    const TRADER_FINISHED = 0;
    const LEADER_FINISHED = 1;

    // 团队红利结算状态，0无红利，1待结算，2已结算
    const BONUS_NONE   = 0;
    const BONUS_UNPAID = 1;
    const BONUS_PAID   = 2;

    //  团队红利结算状态文本
    const TEAM_BONUS_STATUS = [
        self::BONUS_NONE   => '无红利',
        self::BONUS_UNPAID => '待结算',
        self::BONUS_PAID   => '已结算',
    ];

    // TTK交易收币状态，1确认中，2完成
    const CONFIRMING = 1;
    const FINISHED   = 2;

    const HASH_STATUS = [
        self::CONFIRMING => ['name'=>'确认中', 'class'=>''],
        self::FINISHED   => ['name'=>'完成',   'class'=>''],
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
     * 关联商户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id')
            ->select('id', 'username', 'email', 'phone');
    }

    /**
     * 获取该订单的币种信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')
            ->select(['id', 'currency_title_en_abbr as abbr','currency_title_cn']);
    }

    /**
     * 获取该订单的法币信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function legalCurrency()
    {
        return $this->belongsTo(LegalCurrency::class, 'legal_currency_id')
            ->select(['id', 'abbr','name']);
    }

    /**
     * 筛选申诉状态
     *
     * @param $query
     * @param $appealStatus
     * @return mixed
     */
    public static function scopeAppealStatus($query, $appealStatus)
    {
        return $query->where('appeal_status', $appealStatus);
    }

    /**
     * 筛选状态
     *
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 筛选类型
     *
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 筛选币种
     *
     * @param $query
     * @param $currency
     * @return mixed
     */
    public function scopeCurrency($query, $currency)
    {
        return $query->where('currency_id', $currency);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getPriceAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getFieldAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getCashAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getFeeAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getFinalAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getTeamBonusAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getRateAttribute($value)
    {
        return floatval($value);
    }

}
