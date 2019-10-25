<?php

namespace App\Models\Wallet;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    /**
     * 交易用户记账钱包
     *
     * @var string
     */
    protected $table = 'wallet_balances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取usdt余额
     *
     * @param $userId
     * @return mixed
     */
    public static function getUsdtBalance($userId)
    {
        return self::lockForUpdate()->getBalance($userId, Currency::USDT);
    }

    /**
     * 获取币种信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'user_wallet_currency_id')
            ->select(['id', 'currency_title_en_abbr as abbr']);
    }
}
