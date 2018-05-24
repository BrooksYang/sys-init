<?php

/*
|--------------------------------------------------------------------------
| Mongo Log Routes
|--------------------------------------------------------------------------
|
| 系统管理端日志路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen']], function()
{

    //管理端日志列表
    Route::get('backend/log', 'Log\BackendLogController@index');


});




