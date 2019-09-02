<?php

namespace App\Models\Wallet;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /**
     * 交易用户的真实钱包地址
     *
     * @var string
     */
    protected $table = 'wallets';

    protected $guarded = [];

    // 钱包类型，1提币地址(用户添加)，2充值地址(系统生成)',
    const WITHDRAW_ADDRESS = 1;
    const DEPOSIT_ADDRESS = 2;

    const TYPE = [
        1 => ['name' => '提币地址', 'class' => ''],
        2 => ['name' => '充值地址', 'class' => '']
    ];
}
