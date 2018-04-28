<?php

/*
|--------------------------------------------------------------------------
| Issue Routes
|--------------------------------------------------------------------------
|
| 机构发币路由
|
*/


Route::group(['middleware' => ['web']], function()
{
    //发币方账号初始化
    Route::resource('issuer/issurerInit', 'Issue\IssueController');

    //币种初始化
    Route::resource('issuer/currencyTypeInit', 'Issue\CurrencyTypeInitController');

    //币种类型管理
    Route::resource('issuer/currencyTypeMg', 'Issue\CurrencyTypeMgController');

    //用户代币交易合约
    Route::resource('issuer/userCurrencyContract','Issue\CurrencyContractToUserController');
});




