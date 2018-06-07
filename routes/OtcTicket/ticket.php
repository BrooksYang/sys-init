<?php

/*
|--------------------------------------------------------------------------
| OTC system work card
|--------------------------------------------------------------------------
|
| OTC 交易系统工单
|
*/

/**
 *  工单客服管理模块
 */

Route::group(['prefix' => 'ticket/supervisor'], function()
{
     Route::get('index','Ticket\SupervisorController@index');
     Route::get('create','Ticket\SupervisorController@create');
     Route::get('edit/{id}','Ticket\SupervisorController@edit');
     Route::get('reset/{id}','Ticket\SupervisorController@reset');
     Route::post('store','Ticket\SupervisorController@store');
     Route::post('update/{id}','Ticket\SupervisorController@update');
     Route::post('savePassword/{id}','Ticket\SupervisorController@savePassword');
     Route::post('onVacation','Ticket\SupervisorController@onVacation');
     Route::post('backToWork','Ticket\SupervisorController@backToWork');
});

/**
 *  工单处理
 */
Route::group(['prefix' => 'ticket/handler'], function()
{
     Route::get('index','Ticket\HandlerController@index');
     Route::get('task','Ticket\HandlerController@task'); // 我的任务
     Route::get('getTask','Ticket\HandlerController@getTask'); // 获取我的任务
     Route::get('delete/{id}','Ticket\HandlerController@destroy'); // 删除工单及回复
     Route::get('deleteReply/{id}','Ticket\HandlerController@deleteReply');
     Route::get('detail/{id}','Ticket\HandlerController@detail'); // 工单处理详情页面
     Route::get('supervisor/{id}','Ticket\HandlerController@supervisor'); // 查看客服信息
     Route::get('ticketTransfer/{id}','Ticket\HandlerController@ticketTransfer'); // 转移工单操作页面
     Route::post('transfer','Ticket\HandlerController@transfer'); // 转移工单操作页面
     Route::post('ticketReply','Ticket\HandlerController@ticketReply'); // 回复工单
     Route::post('replyLevelTwo','Ticket\HandlerController@replyLevelTwo'); // 回复工单二级

});


