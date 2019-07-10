<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

//订单状态：1 等待受理 2 处理中 3 已发币 4 失败,
const WAITING_FOR_WITHDRAWS = 1;
const WITHDRAW_PROCESSINGS = 2;
const ALREADY_WITHDRAWS = 3;
const WITHDRAW_FAILEDS =4;

/**
 * Class UserOtcWithdrawOrder
 * @package App\Http\Middleware
 * OTC 用户提币-钱包可用余额及操作检查
 */
class UserOtcWithdrawOrder
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

                return response()->json(['code'=>200061 ,'msg' => '非法操作']);
            }
        }

        if ($withdrawOrder && $orderStatus == ALREADY_WITHDRAWS) {
            if (!$this->checkBalance($withdrawOrder)) {

                return response()->json(['code'=>200060 ,'msg' => '钱包可用余额不足']);
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
        $orderSrcStatus = DB::table('otc_withdraws as w_order')
            ->where('w_order.id', $withdrawOrder)->value('status');

        $actionStatus = in_array($orderStatus, [WAITING_FOR_WITHDRAWS,WITHDRAW_PROCESSINGS,WITHDRAW_FAILEDS]);
        if (($orderSrcStatus == ALREADY_WITHDRAWS) && $actionStatus) {
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
        $withdrawOrderInfo = DB::table('otc_withdraws as w_order')
            ->join('otc_balances as u_wallet', 'w_order.currency_id', 'u_wallet.currency_id')
            ->where('w_order.id', $withdrawOrder)
            ->get(['w_order.currency_id', 'w_order.amount', 'available', 'frozen'])
            ->first();

        $validBalance = $withdrawOrderInfo->available - $withdrawOrderInfo->amount >= $withdrawOrderInfo->frozen;

        return $validBalance;
    }
}
