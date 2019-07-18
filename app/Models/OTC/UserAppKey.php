<?php

namespace App\Models\OTC;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UserAppKey extends Model
{
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
            ->select('id','username','phone','email','id_number');
    }

}
