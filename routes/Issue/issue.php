<?php

/*
|--------------------------------------------------------------------------
| Issue Routes
|--------------------------------------------------------------------------
|
| 币种及交易合约路由
|
*/


Route::group(['middleware' => ['web']], function()
{

    //币种初始化
    Route::resource('issuer/currencyTypeInit', 'Issue\CurrencyTypeInitController');

    //币种类型管理
    Route::resource('issuer/currencyTypeMg', 'Issue\CurrencyTypeMgController');

    //用户代币交易合约
    Route::resource('issuer/userCurrencyContract','Issue\CurrencyContractToUserController');
});




