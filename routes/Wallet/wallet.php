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


/**
 * OTC 运营方收益管理
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    // OTC 运营方提币地址管理（外部地址）
    Route::resource('otc/sys/withdrawAddr', 'Wallet\OtcSysWithdrawAddrController',['except' => ['create','edit','show']]);

    // 更新外部提币地址状态 - 启用或停用
    Route::patch('otc/sys/withdrawAddr/toggle/{id}', 'Wallet\OtcSysWithdrawAddrController@toggle');

    // 添加提币申请 - 发起提币
    Route::post('otc/sys/withdraw', 'Wallet\OtcSysWithdrawAddrController@withdraw');

    // OTC 收益列表
    Route::get('otc/sys/income', 'Wallet\OtcSysIncomeController@index');

    // OTC 运营方提币记录
    Route::get('otc/sys/withdrawLog','CryptoWallet\WalletTransactionController@sysWithdraw');
});





