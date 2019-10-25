<?php

namespace App\Models\OTC;

use Illuminate\Database\Eloquent\Model;

class OtcConfig extends Model
{
    /**
     * @var string
     */
    protected $table = 'otc_config';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * 获取领导人保证金
     *
     * @return mixed
     */
    public static function leaderMargin()
    {
        $margin = self::firstOrNew(['key'=> 'release_order_margin'], ['value' => config('conf.leader_margin'),
            'title' => '发布广告所需保证金']);

        return $margin->value;
    }
}
