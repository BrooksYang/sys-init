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
