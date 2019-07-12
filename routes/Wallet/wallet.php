<?php

/*
|--------------------------------------------------------------------------
| Wallet Routes
|--------------------------------------------------------------------------
|
| 交易用户及运营方记账钱包、OTC 记账钱包路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //交易用户记账钱包
    Route::resource('user/wallet', 'Wallet\UserWalletController');

    //运营方记账钱包
    Route::resource('sys/wallet', 'Wallet\SysWalletController');

});

/**
 * OTC 记账钱包
 * 已废弃 - otc_balances已经与 wallet_balances 合并
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    // 交易用户 OTC 记账钱包
    //Route::resource('otc/user/wallet', 'Wallet\UserOtcWalletController',['except' => ['create', 'store', 'update']]);


});





