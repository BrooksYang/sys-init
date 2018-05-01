<?php

/*
|--------------------------------------------------------------------------
| User Deposit and Withdraw Order Routes
|--------------------------------------------------------------------------
|
| 充值和提币订单路由
|
*/

use Illuminate\Http\Request;

Route::group(['middleware' => ['web']], function()
{
    //用户充值订单
    Route::resource('order/userDeposit', 'Order\UserDepositOrderController');

    //用户提币订单
    Route::resource('order/withdraw', 'Order\UserWithdrawOrderController')->middleware('order.withdraw');

});




