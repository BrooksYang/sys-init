<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{

    /**
     * 用户记账钱包余额表
     *
     * @var string
     */
    protected $table = 'wallets_balances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];




}
