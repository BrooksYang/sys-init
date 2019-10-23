<?php

namespace App;

use App\Models\Bonuses\Bonuses;
use App\Models\KycLevel;
use App\Models\OTC\Trade;
use App\Models\OTC\UserAppKey;
use App\Models\UserFeeConfig;
use App\Models\Wallet\Balance;
use App\Utilities\EtherScan;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // 状态，0禁用，1正常
    const FORBIDDEN = 0;
    const ACTIVE    = 1;

    // 状态文本
    const STATUS = [
        self::FORBIDDEN => ['name' => '禁用', 'class' => 'danger'],
        self::ACTIVE    => ['name' => '启用', 'class' => 'success']
    ];

    // 认证状态，1未认证，2待审核，3已认证，4认证失败
    const NOT_VERIFY = 1;
    const UNDER_VERIFY = 2;
    const VERIFIED = 3;
    const VERIFY_FAILED = 4;

    // 是否为商户，0否，1是
    const NOT_MERCHANT = 0;
    const MERCHANT = 1;

    // 用户类型 0全部，1商户，2币商
    const USERS     = 0;
    const MERCHANTS = 1;
    const TRADERS   = 2;

    const USER_TYPE = [
        self::USERS    => ['name' => '全部', 'class' => ''],
        self::MERCHANT => ['name' => '商户', 'class' => ''],
        self::TRADERS  => ['name' => '币商', 'class' => '']
    ];

    // 领导人级别 0-非领导人，1-领导人
    const COMMON           = 0;
    const LEADER_LEVEL_ONE = 1;

    // 账户类型，0普通用户，1领导人，2搬砖工
    const TYPE_USER   = 0;
    const TYPE_LEADER = 1;
    const TYPE_TRADER = 2;

    // 是否已缴纳保证金，0否，1是
    const NOT_MARGIN = 0;
    const IS_MARGIN  = 1;

    /**
     * 关联 App Key（商户获取App Key）
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function appKey()
    {
        return $this->hasOne(UserAppKey::class, 'user_id');
    }

    /**
     * 关联所属商户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function merchantAppKey()
    {
        return $this->belongsTo(UserAppKey::class, 'access_key', 'access_key');
    }

    /**
     * 关联用户手续费配置
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feeConfig()
    {
        return $this->hasOne(UserFeeConfig::class, 'user_id');
    }

    /**
     * 关联用户分润
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bonuses()
    {
        return $this->hasMany(Bonuses::class, 'user_id');
    }

    /**
     * 关联分润贡献者
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributor()
    {
        return $this->hasMany(Bonuses::class, 'contributor_id');
    }

    /**
     * otc 广告
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trades()
    {
        return $this->hasMany(Trade::class, 'user_id');
    }

    /**
     * 获取用户钱包指定币种余额
     *
     * @param $currency
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function balance($currency)
    {
        return $this->hasOne(Balance::class, 'user_id')
            ->where('user_wallet_currency_id', $currency)
            ->select('id','user_wallet_balance','user_wallet_balance_freeze_amount')
            ->first();
    }

    /**
     * 获取商户
     *
     * @return \Illuminate\Support\Collection
     */
    public function scopeMerchant()
    {
        // 排除模拟及测试账户
        return self::where('is_merchant', self::MERCHANTS)
            ->whereNotIn('id', [26])
            ->get(['username','phone','id']);
    }

    /**
     * 获取认证的币商
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getTraders()
    {
        // 排除模拟账户-包含测试账户
        return self::where('kyc_level_id', KycLevel::ADVANCED)
            ->where('id', '>=' ,133)
            ->orWhereIn('id', [88,89,122])
            ->get(['username','phone','email','id','pid']);
    }

    /**
     * 获取认证的币商
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getTradersInfo()
    {
        // 排除模拟账户-包含测试账户
        return self::with(['feeConfig'])
            ->where('kyc_level_id', KycLevel::ADVANCED)
            ->where('id', '>=' ,133)
            ->orWhereIn('id', [88,89,122])
            ->get(['username','phone','email','id','pid','leader_level','leader_id']);
    }

    /**
     * 获取系统领导人
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getLeaders()
    {
        return self::where('leader_level', '>',0)
            ->get(['username','phone','email','id','pid','leader_level']);
    }

    /**
     * 获取系统提币地址余额 - Erc20-token(USDT)
     *
     * @param string $address
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSysWithDrawAddrBalance($address)
    {
        // debug模式下不再请求ethserscan
        if (config('conf.app_debug')) {
            return 0;
        }

        $etherScan = new  EtherScan();

        $usdtBalance = $etherScan->getTokenBalance($address, config('blockChain.usdt.contract'));

        @$usdtBalance /= pow(10, 6);

        return $usdtBalance;
    }


    /**
     * 获取系统储值地址余额 - Erc20-token(USDT)
     *
     * @param $address
     * @return float|int|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getSysDepositAddrBalance($address)
    {
        // debug模式下不再请求ethserscan
        if (config('conf.app_debug')) {
            return 0;
        }

        $etherScan = new  EtherScan();

        $usdtBalance = $etherScan->getTokenBalance($address, config('blockChain.usdt.contract'));

        @$usdtBalance /= pow(10, 6);

        return $usdtBalance;
    }

}
