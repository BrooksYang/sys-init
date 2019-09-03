<?php

namespace App\Models\Wallet;

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
}
