<?php

namespace App\Models\Wallet;

use App\Models\Currency;
use App\User;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    /**
     * @var string 
     */
    protected $table = 'wallet_transactions';

    /**
     * @var array 
     */
    protected $guarded = [];
    

    // 类型，1充值，2提币
    const DEPOSIT  = 1;
    const WITHDRAW = 2;

    const TYPE = [
        self::DEPOSIT  => ['name'=>'充值', 'class'=>''],
        self::WITHDRAW => ['name'=>'提币', 'class'=>'']
    ];

    // 状态，1处理中，2成功，3失败, 4撤销
    const PENDING  = 1;
    const SUCCESS  = 2;
    const FAILED   = 3;
    const CANCELED = 4;

    const STATUS = [
      self::PENDING  => ['name'=>'处理中', 'class'=>'info'],
      self::SUCCESS  => ['name'=>'成功', 'class'=>'success'],
      self::FAILED   => ['name'=>'失败', 'class'=>'default'],
      self::CANCELED => ['name'=>'撤销', 'class'=>'waring'],
    ];
    

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id','username','email','phone');
    }

    /**
     * 关联币种
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')
            ->select('id','currency_title_en_abbr');
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
     * 格式化金额
     *
     * @param $value
     * @return string
     */
    public function getAmountAttributes($value)
    {
        return number_format($value, 8);
    }

    /**
     * 格式化手续费
     *
     * @param $value
     * @return string
     */
    public function getFeeAttributes($value)
    {
        return number_format($value, 8);
    }


}
