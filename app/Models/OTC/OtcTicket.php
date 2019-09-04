<?php

namespace App\Models\OTC;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * OTC工单
 *
 * Class OtcTicket
 * @package App\Models\OTC
 */
class OtcTicket extends Model
{
    /**
     * @var string
     */
    protected $table = 'otc_ticket';

    /**
     * @var array
     */
    protected $guarded = [];

    // 工单状态，1未分配 2已分配 3已回复 4已关闭 5正在处理 6等待处理
    const NOT_ASSIGN = 1;
    const ASSIGNED   = 2;
    const REPLIED    = 3;
    const CLOSED     = 4;
    const PROCESSING = 5;
    const PENDING    = 6;

    const STATUS = [
        1 => ['name' => '未分配', 'class' => 'info'],
        2 => ['name' => '已分配', 'class' => 'primary'],
        3 => ['name' => '已回复', 'class' => 'default'],
        4 => ['name' => '已关闭', 'class' => 'danger'],
        5 => ['name' => '处理中', 'class' => 'info'],
        6 => ['name' => '待处理', 'class' => 'waring']
    ];

    // 订单类型，1普通otc，2快捷购买',
    const OTC_COMMON = 1;
    const OTC_QUICK  = 2;

    const TYPE = [
        self::OTC_COMMON => ['name' => '普通OTC',  'class' => 'info',   'color'=>'#666666'],
        self::OTC_QUICK  => ['name' => '快捷抢单', 'class' => 'primary', 'color'=>'#a94442'],
    ];


    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id','username','email','phone');
    }

}
