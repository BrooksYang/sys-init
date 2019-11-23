<?php

/*
|--------------------------------------------------------------------------
| CryptoWallet Routes
|--------------------------------------------------------------------------
|
| 交易用户及运营方数字钱包路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin', 'entrance', 'lock.screen','mongo.log']], function()
{
    //交易用户数字钱包
    Route::resource('user/cryptoWallet', 'CryptoWallet\UserCryptoWalletController');

    //运营方数字钱包
    Route::resource('sys/cryptoWallet', 'CryptoWallet\SysCryptoWalletController');

    // 数字钱包交易记录
    Route::resource('wallet/transaction','CryptoWallet\WalletTransactionController');

    // 取消提币
    Route::patch('withdraw/cancel/{withdrawId}', 'CryptoWallet\WalletTransactionController@cancelWithdraw');

});




