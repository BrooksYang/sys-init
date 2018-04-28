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

   /* Route::resource('backup', 'BackupController', ['only' => [
        'index', 'store' ,'destroy'
    ]]);*/
});



