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

    // 定义状态，0确认中，1已下单，2已支付，3已发币，4已完成，5已取消
    const PENDING = 0;
    const ORDERED = 1;
    const PAID = 2;
    const RELEASED = 3;
    const RECEIVED = 4;
    const CANCELED = 5;

    // 状态文本
    const STATUS = [
        self::PENDING  => ['name' => '确认中', 'class' => 'info'], //ttk订单
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

    // 给币商额外补贴状态 0未比对，1已比对
    const SUBSIDY_EXTRA_NULL = 0;
    const SUBSIDY_EXTRA_DONE = 1;

    // TTK交易转账状态，1确认中，2成功，3失败，默认0
    const HASH_NOT_RELEASE = 0;
    const HASH_PENDING     = 1;
    const HASH_SUCCESS     = 2;
    const HASH_FAILED      = 3;

    const HASH_STATUS = [
        self::HASH_NOT_RELEASE => ['name'=>'未发币', 'class'=>''],
        self::HASH_PENDING => ['name'=>'确认中', 'class'=>''],
        self::HASH_SUCCESS => ['name'=>'成功',   'class'=>''],
        self::HASH_FAILED  => ['name'=>'失败',   'class'=>''],
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
     * 待抢单数量
     *
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeUnGrab($query, $time)
    {
        return  $query->status(self::ORDERED)->where('user_id',0)
            ->where('updated_at','like',"$time%")->count();
    }

    /**
     * 待支付订单数量
     *
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeUnPay($query, $time)
    {
        return $query->status(self::ORDERED)->where('user_id','>',0)
            ->where(function ($query) {
                $query->whereNull('appeal_status')->orWhere('appeal_status',self::APPEAL_CANCELED);
            })->where('updated_at','like',"$time%")->count();
    }

    /**
     * 已完成订单数量
     *
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeFinished($query,$time)
    {
        return $query->status(self::RECEIVED)->where('updated_at','like',"$time%")->count();
    }

    /**
     * 问题订单数量
     *
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeAppealing($query, $time)
    {
        return $query->status(self::ORDERED)->whereIn('appeal_status',[self::APPEALED,self::APPEALING])
            ->where('updated_at','like',"$time%")->count();
    }

    /**
     * 订单总数量
     *
     * @param $query
     * @param $time
     * @return mixed
     */
    public function scopeTotal($query, $time)
    {
        return $query->where('updated_at','like',"$time%")->count();
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

    /**
     * 数值格式化
     *
     * @param $value
     * @return float
     */
    public function getSubsidyTraderAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return float
     */
    public function getSubsidySysAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     *
     * @param $value
     * @return float
     */
    public function getSubsidyTraderExtraAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getCurrencyRateAttribute($value)
    {
        return floatval($value);
    }


    /**
     * 格式化数据
     *
     * @param $value
     * @return float
     */
    public function getSendAmountAttribute($value)
    {
        return floatval($value);
    }

}

