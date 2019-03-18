<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcPayPath extends Model
{
    /**
     * otc用户支付信息
     * @var string
     */
    protected  $table = 'otc_pay_paths';

    protected  $guarded = [];
}
