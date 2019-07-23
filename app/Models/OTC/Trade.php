<?php

namespace App\Models\OTC;

use App\Models\Currency;
use App\Models\LegalCurrency;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * 交易广告
 *
 * Class Trade
 * @package App\Models\OTC
 */
class Trade extends Model
{
    /**
     * 广告
     *
     * @var string
     */
    protected $table = 'otc_advertisements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 定义类型,类型，1买单，2卖单
    const BUY = 1;
    const SELL = 2;

    // 状态，1进行中，2已完成，3已下架，4已撤销
    const ON_SALE = 1;
    const FINISHED = 2;
    const OFF = 3;
    const CANCELLED = 4;

    // 是否需要高级认证，0不需要，1需要
    const NEED_ADVANCED_AUTH = 1;

    /**
     * 获取该广告的币种信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')
            ->select(['id', 'currency_title_en_abbr as abbr']);
    }

    /**
     * 获取该广告的法币信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function legalCurrency()
    {
        return $this->belongsTo(LegalCurrency::class, 'legal_currency_id')
            ->select(['id', 'abbr']);
    }

    /**
     * 获取广告发布者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select(['id', 'username', 'email', 'phone']);
    }

    /**
     * @param $value
     * @return float
     */
    public function getAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * @param $value
     * @return float
     */
    public function getFieldAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * @param $value
     * @return float
     */
    public function getFloorAttribute($value)
    {
        return floatval($value);
    }

    /**
     * @param $value
     * @return float
     */
    public function getCeilingAttribute($value)
    {
        return floatval($value);
    }

    /**
     * @param $value
     * @return float
     */
    public function getPriceAttribute($value)
    {
        return floatval($value);
    }
}
