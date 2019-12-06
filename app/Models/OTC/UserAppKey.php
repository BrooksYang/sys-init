<?php

namespace App\Models\OTC;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserAppKey extends Model
{

    // 是否开启：0关闭，1开启
    const CLOSE = 0;
    const OPEN  = 1;

    const IS_OPEN = [
        self::CLOSE => ['name' => '关闭', 'class'=>'danger'],
        self::OPEN  => ['name' => '开启', 'class'=>'success']
    ];

    // 商户类型，0普通，1BC商户, 2TTK商户
    const COMMON = 0;
    const BC     = 1;
    const TTK    = 2;

    const TYPE = [
        self::COMMON => ['name' => '普通',  'class'=>''],
        self::BC     => ['name' => 'BC',   'class'=>''],
        self::TTK    => ['name' => 'TTK',  'class'=>''],
    ];

    /**
     * 商户密钥管理
     *
     * @var string
     */
    protected $table = 'user_app_keys';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id','username','phone','email','id_number','country_id','is_valid');
    }

    /**
     * 获取商户旗下用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'access_key', 'access_key');
    }

    /**
     * 格式化ip
     *
     * @param $value
     * @return string|null
     */
    public function getIpAttributes($value)
    {
        $ip = json_decode($value, true);

        return count($ip) == 1 ? $ip[0] : (count($ip) > 1 ? implode(',', $ip) : null);
    }

    /**
     * 格式化时间
     *
     * @param $value
     * @return false|string
     */
    public function getStartTimeAttribute($value)
    {
        return $value ? date('H:i',strtotime($value)) : null;
    }

    /**
     * 格式化时间
     *
     * @param $value
     * @return false|string
     */
    public function getEndTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

}
