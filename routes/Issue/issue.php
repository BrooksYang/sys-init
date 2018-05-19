<?php

/*
|--------------------------------------------------------------------------
| Issue Routes
|--------------------------------------------------------------------------
|
| 币种及交易合约路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin']], function()
{

    //币种初始化
    Route::resource('issuer/currencyTypeInit', 'Issue\CurrencyTypeInitController');

    //币种图标上传和裁剪
    Route::post('issuer/currencyIcon/upload/{dir}', 'Issue\CurrencyTypeInitController@upload');
    Route::patch('issuer/currencyIcon/upload/{dir}', 'Issue\CurrencyTypeInitController@upload');
    Route::get('issuer/currencyIcon/crop/{dir}', 'Issue\CurrencyTypeInitController@crop');

    //币种类型管理
    Route::resource('issuer/currencyTypeMg', 'Issue\CurrencyTypeMgController');

    //用户代币交易合约
    Route::resource('issuer/userCurrencyContract','Issue\CurrencyContractToUserController');

    //交易对费率
    Route::post('userCurrencyContract/symbol/fee','Issue\CurrencyContractToUserController@symbolFeeUpdate');
});




