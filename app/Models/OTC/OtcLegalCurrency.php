<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcLegalCurrency extends Model
{
    /**
     * 系统支持的法币
     * @var string
     */
    protected $table = 'legal_currencies';

    protected $guarded = [];

}
