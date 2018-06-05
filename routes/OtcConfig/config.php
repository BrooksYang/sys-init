<?php

/*
|--------------------------------------------------------------------------
| OTC system config route
|--------------------------------------------------------------------------
|
| OTC 交易系统配置管理路由
|
*/

/**
 * OTC系统配置管理
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen', 'mongo.log']], function()
{
    //系统配置管理
    Route::resource('otc/config', 'OtcConfig\ConfigController', ['only' => ['edit', 'update']]);

    //系统支付类型
    Route::resource('otc/payType', 'OtcConfig\OtcPayTypeController');

    //系统法币
    Route::resource('otc/legalCurrency', 'OtcLegalCurrency\LegalCurrencyController');

});



