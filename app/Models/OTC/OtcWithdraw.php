<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcWithdraw extends Model
{

    // 订单状态：1 等待受理 2 处理中 3 已发币 4 失败',
    const OTC_WAITING  = 1;
    const OTC_PENDING  = 2;
    const OTC_RELEASED = 3;
    const OTC_FAILED   = 4;

    // 状态文本
    public static $statusTexts = [
        self::OTC_WAITING  => '等待受理',
        self::OTC_PENDING  => '处理中',
        self::OTC_RELEASED => '已发币',
        self::OTC_FAILED   => '失败',
    ];

    /**
     * OTC提现订单
     *
     * @var string
     */
    protected $table = 'otc_withdraws';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取支付信息
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payPath()
    {
        return $this->belongsTo(OtcPayPath::class, 'pay_path_id');
    }




}
