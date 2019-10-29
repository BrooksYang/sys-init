<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{

    /**
     * CREATE TABLE `merchant_trader` (
        `user_id` int(11) NOT NULL,
        `merchant_id` int(11) NOT NULL COMMENT 'user_app_key_id',
     PRIMARY KEY (`user_id`,`merchant_id`) USING BTREE,
     KEY `user_user_app_key_id_index` (`user_id`) USING BTREE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户/商户关联';
     */

    /**
     * 币商与商户关联
     *
     * @var string
     */
    protected  $table = 'merchant_trader';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

}
