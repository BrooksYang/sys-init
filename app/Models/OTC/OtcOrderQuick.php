<?php

namespace App\Models\OTC;

use App\User;
use Illuminate\Database\Eloquent\Model;

class OtcOrderQuick extends Model
{
    /**
     * OTC 商户出金-币商快捷抢单买入
     *
     * @var string
     */
    protected $table = 'otc_orders_quick';

    protected $guarded = [];

    // 定义状态，1已下单，2已支付，3已发币，4已完成，5已取消
    const ORDERED = 1;
    const PAID = 2;
    const RELEASED = 3;
    const RECEIVED = 4;
    const CANCELED = 5;

    // 状态文本
    const STATUS = [
        self::ORDERED  => ['name' => '已下单', 'class' => 'info'],
        self::PAID     => ['name' => '已支付', 'class' => 'primary'],
        self::RELEASED => ['name' => '已发币', 'class' => 'warning'], // 3确认收款(已发币)
        self::RECEIVED => ['name' => '已完成', 'class' => 'success'], // 4确认收币
        self::CANCELED => ['name' => '已取消', 'class' => 'default']
    ];


    // 申诉状态，1已申诉，2申诉处理中，3申诉完结, 4已撤诉
    const NOT_APPEAL      = 0;
    const APPEALED        = 1;
    const APPEALING       = 2;
    const APPEAL_END      = 3;
    const APPEAL_CANCELED = 4;

    // 申诉状态文本
    const APPEAL_STATUS = [
        self::NOT_APPEAL => ['name' => '无申诉', 'class' => 'default'],
        self::APPEALED   => ['name' => '已申诉', 'class' => 'danger'],
        self::APPEALING  => ['name' => '处理中', 'class' => 'warning'],
        self::APPEAL_END => ['name' => '已完结', 'class' => 'info'],
        self::APPEAL_CANCELED => ['name' => '已撤诉', 'class' => 'default'],
    ];


    /**
     * 关联币商
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id','username','phone','email');
    }

    /**
     * 关联商户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id')
            ->select('id','username', 'email', 'phone');
    }

    /**
     * 关联发布者-商户旗下用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
         return $this->belongsTo(User::class, 'owner_id')
             ->select('id','username','phone');
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
     * 币商检查 - 白名单或有交易额
     *
     * @param $traders
     * @return mixed
     */
    public static function traderCheck($traders)
    {
        // 在白名单或虽不在白名单但有交易额的币商
        $whiteList = explode(',', config('conf.otc_quick_order_white_list'));

        $traders = $traders->filter(function ($item) use ($whiteList){
            $inWhiteList = in_array($item->id, $whiteList);
            $amount = self::status(self::RECEIVED)
                ->where('user_id', $item->id)
                ->sum('field_amount');

            return $inWhiteList || $amount;
        });

        return $traders;
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getMerchantAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getMerchantFinalAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getCashAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getFieldAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getPriceAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getRateAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getRateSysAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getIncomeTotalAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getIncomeSysAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getIncomeMerchantAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return string
     */
    public function getIncomeUserAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return float
     */
    public function getSubsidyAttribute($value)
    {
        return floatval($value);
    }

}

