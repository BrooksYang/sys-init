<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycLevel extends Model
{
    /**
     * KYC认证等级表
     *
     * @var string
     */
    protected $table = 'kyc_levels';

    /**
     * 批量赋值字段
     *
     * @var array
     */
    protected $guarded = [];

    // 1初级认证，2高级认证
    const COMMON   = 1;
    const ADVANCED = 2;

}
