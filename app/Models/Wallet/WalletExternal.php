<?php

namespace App\Models\Wallet;

use App\Models\Currency;
use App\Models\OTC\OtcOrder;
use App\User;
use Illuminate\Database\Eloquent\Model;

class WalletExternal extends Model
{
    /**
     * 外部地址
     *
     * @var string
     */
    protected $table = 'wallets_external';

    protected $guarded = [];

    // 类型，1提币地址
    const WITHDRAW_ADDR = 1;

    const TYPE = [
        1 => ['name' => '提币地址', 'class' => '']
    ];

    //  状态，1启用，2停用
    const ENABLE  = 1;
    const DISABLE = 2;

    const STATUS = [
        1 => ['name' => '启用', 'class' => 'info'],
        2 => ['name' => '停用', 'class' => 'default']
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
     * 获取外部提币地址
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAddr()
    {
        return self::get(['id','address', 'desc']);
    }

    /**
     * 是否存在已激活的提币地址
     *
     * @param $uid
     * @param $type
     * @return bool
     */
    public static function addrEnabled($uid = 0, $type = self::WITHDRAW_ADDR)
    {
        $exists = WalletExternal::where($uid)->type($type)
            ->status(WalletExternal::ENABLE)->exists() ?: false;

        return $exists;
    }

    /**
     * OTC 可提数额
     *
     * @return string
     */
    public static function available()
    {
        bcscale(config('app.bcmath_scale'));

        $orderBuyFee = self::otcOrderTotal();
        $depositFee = self::walletTransFee();

        return bcadd($orderBuyFee, $depositFee);
    }

    /**
     * OTC 订单交易手续费 - 默认买入-USDT
     *
     * @param int $type
     * @param int $currency
     * @return mixed
     */
    public static function otcOrderTotal($type = OtcOrder::BUY, $currency = Currency::USDT)
    {
        $orderFee =  OtcOrder::type($type)
            ->currency($currency)
            ->status(OtcOrder::RECEIVED)
            ->sum('fee');

        return $orderFee;
    }

    /**
     * OTC 钱包交易手续费 - 默认充值-USDT
     *
     * @param $type
     * @param $currency
     * @return mixed
     */
    public static function walletTransFee($type = WalletTransaction::DEPOSIT,  $currency = Currency::USDT)
    {
        $walletTransFee = WalletTransaction::type($type)
            ->currency($currency)
            ->status(WalletTransaction::SUCCESS)
            ->sum('fee');

        return $walletTransFee;
    }
}
