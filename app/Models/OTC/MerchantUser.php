<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{
    /**
     * 币商与商户关联
     *
     * @var string
     */
    protected  $table = 'merchant_trader';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

}
