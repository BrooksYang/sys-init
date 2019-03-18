<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcPayType extends Model
{
    /**
     * otc系统支付类型
     * @var string
     */
    protected  $table = 'otc_pay_types';

    protected  $guarded = [];
}
