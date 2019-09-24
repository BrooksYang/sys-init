<?php

namespace App\Models\NeuContract;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
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
    protected $table = 'wallets_transactions';

    // NeuContract 用户UID
    const NEU_CONTRACT_OTC = 2;

    // 状态，1处理中，2成功，3失败
    const PENDING = 1;
    const SUCCESS = 2;
    const FAILED  = 3;

    //类型, 1充值，2提币，3归集，4gas，5自动提币（打款至商户外部地址），6手续费提取
    const DEPOSIT       = 1;
    const WITHDRAW      = 2;
    const COLLECT       = 3;
    const GAS           = 4;
    const WITHDRAW_AUTO = 5;
    const WITHDRAW_FEE  = 6;

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
     * 获取系统待归集数额
     *
     * @return mixed
     */
    public static function sysCollectPending()
    {
        return self::where('user_id', self::NEU_CONTRACT_OTC)
            ->type(self::COLLECT)
            ->status(self::PENDING)
            ->sum('amount');
    }

}
