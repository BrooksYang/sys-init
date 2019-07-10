<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalCurrency extends Model
{
    // 定义人民币、美元id
    const RMB = 1;
    const DOLLAR = 2;

    /**
     * @var string
     */
    protected $table = 'legal_currencies';

    /**
     * @var array
     */
    protected $guarded = [];
}
