<?php

/*
|--------------------------------------------------------------------------
| CryptoWallet Routes
|--------------------------------------------------------------------------
|
| 交易用户及运营方数字钱包路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin']], function()
{
    //交易用户数字钱包
    Route::resource('user/cryptoWallet', 'CryptoWallet\UserCryptoWalletController');

    //运营方数字钱包
    Route::resource('sys/cryptoWallet', 'CryptoWallet\SysCryptoWalletController');

});



