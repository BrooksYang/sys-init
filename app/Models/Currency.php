<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    // 定义常用币种id（注意，该币种id数据库不能修改）
    const ETH  = 1;
    const BTC  = 2;
    const USDT = 3;
    const TTK  = 6;

    const LG = 10;
    const FWB = 11;
    const HED = 12;

    /**
     * 币种信息
     *
     * @var string
     */
    protected $table = 'currencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取币种
     *
     * @return \Illuminate\Support\Collection
     */
    public static  function getCurrencies()
    {
      return  Currency::all()->pluck('currency_title_en_abbr', 'id');
    }
}
