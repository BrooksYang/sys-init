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
    //配置管理
    Route::resource('otc/config', 'OtcConfig\ConfigController', ['except' => [
        'create', 'store', 'index', 'show', 'destroy']]);


});



