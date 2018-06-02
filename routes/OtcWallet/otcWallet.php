<?php

/*
|--------------------------------------------------------------------------
| OTC Wallet Routes
|--------------------------------------------------------------------------
|
| 交易用户 OTC 记账钱包路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //交易用户 OTC 记账钱包
    Route::resource('otc/user/wallet', 'OtcWallet\UserOtcWalletController',['except' => ['create', 'store',
        'edit', 'update']]);


});




