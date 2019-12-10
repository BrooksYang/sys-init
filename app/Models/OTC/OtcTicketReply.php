<?php

namespace App\Models\OTC;

use App\Admin;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * 工单回复
 *
 * Class OtcTicketReply
 * @package App\Models\OTC
 */
class OtcTicketReply extends Model
{
    protected $table = 'otc_ticket_reply';

    protected $guarded = [];

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->select('id','username','phone','email','is_merchant','access_key');
    }

    /**
     * 关联客服管理员
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(Admin::class, 'owner_id')
            ->select('id','name','email');
    }

}
