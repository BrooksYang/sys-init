<?php

/*
|--------------------------------------------------------------------------
| User Deposit and Withdraw Order Routes
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
    Route::resource('order/userDeposit', 'Order\UserDepositOrderController');

    //用户提币订单
    Route::resource('order/withdraw', 'Order\UserWithdrawOrderController', ['only' => ['index','destroy']]);
        //->middleware('order.withdraw');

});

/**
 * 交易订单
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //用户交易订单
    Route::resource('order/exchange', 'Order\ExchangeOrderController', ['only' => ['index']]);
});

/**
 * OTC 交易订单
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //用户 OTC 交易订单
    Route::resource('order/otc', 'Order\UserOtcOrderController', ['only' => ['index','destroy']]);
});



