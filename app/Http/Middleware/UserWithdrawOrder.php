<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

//订单状态：1 等待受理 2 处理中 3 已发币 4 失败,
const WAITING_FOR_WITHDRAW = 1;
const WITHDRAW_PROCESSING = 2;
const ALREADY_WITHDRAW = 3;
const WITHDRAW_FAILED =4;

/**
 * Class UserWithdrawOrder
 * @package App\Http\Middleware
 * 用户提币-钱包可用余额检查-提币金额-钱包余额-冻结金额
 */
class UserWithdrawOrder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $withdrawOrder = last(explode('/',$request->path()) );
        $orderStatus = last(explode('=',$request->getQueryString()) );
        if ($withdrawOrder) {
            if (!$this->checkAction($withdrawOrder,$orderStatus)) {

                return response()->json(['code'=>100071 ,'msg' => '非法操作']);
            }
        }

        if ($withdrawOrder && $orderStatus == ALREADY_WITHDRAW) {
            if ($this->checkBalance($withdrawOrder)) {

                return response()->json(['code'=>100070 ,'msg' => '钱包可用余额不足']);
            }
        }

        return $next($request);
    }

    /**
     * 检查所更新订单的状态
     *
     * @param $withdrawOrder
     * @param $orderStatus
     * @return bool
     */
    public function checkAction($withdrawOrder, $orderStatus)
    {
        $orderSrcStatus = DB::table('order_withdraws as w_order')
            ->where('w_order.id', $withdrawOrder)
            ->value('withdraw_order_status');

        $actionStatus = in_array($orderStatus, [WAITING_FOR_WITHDRAW,WITHDRAW_PROCESSING,WITHDRAW_FAILED]);
        if (($orderSrcStatus == ALREADY_WITHDRAW) && $actionStatus) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 重复检查可用余额
     *
     * @param $withdrawOrder
     * @return bool
     */
    public function checkBalance($withdrawOrder)
    {
        $withdrawOrderInfo = DB::table('order_withdraws as w_order')
            ->join('wallet_balances as u_wallet', 'w_order.withdraw_currency_id', 'u_wallet.user_wallet_currency_id')
            ->where('w_order.id', $withdrawOrder)
            ->get(['withdraw_currency_id','withdraw_amount', 'user_wallet_balance', 'user_wallet_balance_freeze_amount'])
            ->first();

        $validBalance = $withdrawOrderInfo->user_wallet_balance <= $withdrawOrderInfo->withdraw_amount;

        return $validBalance;
    }
}
