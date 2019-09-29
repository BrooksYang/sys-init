<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFeeConfig extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_fee_configs';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取系统手续费分润默认配置
     *
     * @return UserFeeConfig|Model|object|null
     */
    public static function sysFeeConfig()
    {
        return self::where('user_id', 0)->first();
    }

}
