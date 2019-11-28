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

    // 类型, 1划转，2冻结，3恢复冻结
    const TRANSFER = 1;
    const FROZEN   = 2;
    const RESUME   = 3;

    const TYPE = [
        self::TRANSFER => ['name' => '划转'],
        self::FROZEN   => ['name' => '冻结'],
        self::RESUME   => ['name' => '恢复']
    ];

    // 冻结是否已恢复；0-否，1-是
    const NOT_RESUME = 0;
    const RESUMED    = 1;

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
