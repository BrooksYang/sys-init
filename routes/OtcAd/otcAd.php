<?php

/*
|--------------------------------------------------------------------------
| OTC Trade Ad
|--------------------------------------------------------------------------
|
| OTC 用户交易广告
|
*/

/**
 * OTC 交易广告
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //用户 OTC 交易广告
    Route::resource('otc/ad', 'OtcAd\OtcAdController', ['only' => ['index','destroy']]);
});




