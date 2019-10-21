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
    // 交易用户记账钱包
    Route::resource('user/wallet', 'Wallet\UserWalletController');

    // 运营方记账钱包
    Route::resource('sys/wallet', 'Wallet\SysWalletController');

    // 用户钱包余额变更记录 - 划转
    Route::get('wallet/balance/log', 'Wallet\UserWalletController@balanceLog');
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

    // OTC 系统总收益查询
    Route::get('otc/sys/income/total', 'Wallet\IncomeTotalController@index');
});


/**
 * OTC 运营方财务日报管理
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    // OTC 运营方收益日报
    Route::get('otc/income/daily', 'Finance\IncomeController@incomeDaily');

    // OTC 运营方收益日报导出
    Route::get('otc/income/daily/export', 'Finance\IncomeController@export');

    // OTC 统计数据概览导出
    Route::get('otc/report', 'Finance\IncomeController@report');

    // OTC 运营方支出日报（收益提取）
    Route::get('otc/expenditure/daily', 'Finance\CostController@index');

    // OTC 运营方支出日报导出
    Route::get('otc/expenditure/daily/exportExcel', 'Finance\CostController@export');

});



