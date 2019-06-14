<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class SysWallet extends Model
{

    /**
     * 系统记账钱包余额表
     *
     * @var string
     */
    protected $table = 'wallet_balances_system';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];




}
