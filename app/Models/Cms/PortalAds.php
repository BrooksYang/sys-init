<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class PortalAds extends Model
{

    /**
     * 系统广告位
     * @var string
     */
    protected $table = 'portal_ads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // 定义位置，1-首页轮播
    const LOCATION_ONE = 1;


}
