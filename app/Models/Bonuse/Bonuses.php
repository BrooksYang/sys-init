<?php

namespace App\Models\Bonuses;

use App\Models\Wallet\WalletTransaction;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Bonuses extends Model
{
    /**
     * @var string
     */
    protected $table = 'bonuses';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 关联贡献者
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contributor()
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    /**
     * 关联交易记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(WalletTransaction::class, 'transaction_id');
    }


}
