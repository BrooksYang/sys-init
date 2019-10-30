<?php

/*
|--------------------------------------------------------------------------
| User management route
|--------------------------------------------------------------------------
|
| 用户管理路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //认证用户待审核
    Route::get('user/manage/pending', 'User\UserController@pendingUser');

    //用户管理
    Route::resource('user/manage', 'User\UserController');

    // 系统用户kyc等级管理 - 暂隐藏KYC等级的维护
    Route::resource('user/kycLevel/manage','User\KycLevelController',['only' => ['index']]);

    // 商户管理
    Route::resource('user/merchant', 'User\UserAppKeyController', ['except' => ['destroy']]);

    // 修改商户账户状态
    Route::patch('change/merchant/status/{id}', 'User\UserAppKeyController@changeAccountStatus');

    // 设置或取消用户账户类型为商户
    Route::patch('user/set/merchant/{id}', 'User\UserAppKeyController@setMerchant');

});

/*
|--------------------------------------------------------------------------
| Trader management route
|--------------------------------------------------------------------------
|
| 币商层级及分润管理路由
|
*/
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    // 币商层级及分润
    Route::resource('user/trader/income', 'User\TraderIncomeController', ['except' => ['create','store','destroy']]);

    // 更新手续费分润 - 系统默认配置
    Route::patch('trader/income/defConfig', 'User\TraderIncomeController@defaultConf');

    // 领导人及搬砖工账号冻结
    Route::patch('user/account/frozen/{uid}', 'User\TraderIncomeController@accountFrozen');

    // 领导人及搬砖工 - 关联商户
    Route::patch('link/merchant/{uid}', 'User\TraderIncomeController@linkMerchant');

});



