<?php

namespace App\Models\NeuContract;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /**
     * 结算平台 - 数据库连接信息
     *
     * @var string
     */
    protected $connection = 'neuContract';

    /**
     * @var string
     */
    protected $table = 'wallets';

    // 类型，0归集，1充值，2提币
    const COLLECT  = 1;
    const DEPOSIT  = 2;
    const WITHDRAW = 3;

    /**
     * 获取系统归集账户可用余额
     *
     * @return mixed
     */
    public static function sysCollectionBalance()
    {
        return self::where('user_id', 0)->value('balance');
    }

}
