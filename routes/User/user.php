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

    // 系统用户kyc等级管理
    Route::resource('user/kycLevel/manage','User\KycLevelController');

});




