<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class SysWallet extends Model
{

    /**
     * OTC钱包余额表
     *
     * @var string
     */
    protected $table = 'dcuex_sys_wallet';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];




}
