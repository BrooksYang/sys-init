<?php

namespace App\Models\Wallet;

use App\Models\Currency;
use App\User;
use Illuminate\Database\Eloquent\Model;

class WalletsBalanceLog extends Model
{
    /**
     * @var string 
     */
    protected $table = 'wallets_balance_logs';

    /**
     * @var array 
     */
    protected $guarded= [];

    // 记录类型，1划转
    const TRANSFER = 1;
    const FROZEN   = 2;

    const TYPE = [
        self::TRANSFER => ['name' => '划转'],
        self::FROZEN   => ['name' => '冻结']
    ];

    /**
     * 关联用户
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id','username','phone','email');
    }

    /**
     * 关联币种
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')
            ->select('id','currency_title_en_abbr as abbr');
    }

    /**
     * 数值格式化
     * @param $value
     * @return float
     */
    public function getAmountAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     * @param $value
     * @return float
     */
    public function getFromAttribute($value)
    {
        return floatval($value);
    }

    /**
     * 数值格式化
     * @param $value
     * @return float
     */
    public function getToAttribute($value)
    {
        return floatval($value);
    }
    
}
