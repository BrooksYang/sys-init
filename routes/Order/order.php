<?php

/*
|--------------------------------------------------------------------------
| User Deposit and Withdraw and OTC Order Routes
|--------------------------------------------------------------------------
|
| 充值和提币订单、用户交易订单路由
|
*/

/**
 * 充值和提币订单
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //用户充值订单
    Route::resource('order/userDeposit', 'Order\UserDepositOrderController',['only' => ['index','update','destroy']]);

    //用户提币订单
    Route::patch('order/withdraw/{withdraw}', 'Order\UserWithdrawOrderController@update')->where('withdraw', '[0-9]+')
        ->middleware('order.withdraw');
    Route::resource('order/withdraw', 'Order\UserWithdrawOrderController', ['only' => ['index','destroy']]);

    // OTC用户充值订单
    Route::resource('order/otc/userDeposit', 'Order\UserOtcDepositOrderController', ['only' => ['index','update','destroy']]);

    // OTC用户提币订单
    Route::patch('order/otc/withdraw/{withdraw}', 'Order\UserOtcWithdrawOrderController@update')->where('withdraw', '[0-9]+')
        ->middleware('order.otcWithdraw');
    Route::resource('order/otc/withdraw', 'Order\UserOtcWithdrawOrderController', ['only' => ['index','destroy']]);

});

/**
 * 交易订单
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //用户交易订单
    Route::resource('order/exchange', 'Order\ExchangeOrderController', ['only' => ['index']]);

    //用户 OTC 交易订单
    Route::resource('order/otc', 'Order\UserOtcOrderController', ['only' => ['index','destroy']]);
});



