<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

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
        //订单状态：1 等待受理 2 处理中 3 已发币 4 失败,
        $withdrawOrder = last(explode('/',$request->path()) );
        $orderStatus = last(explode('=',$request->getQueryString()) );
        if (is_numeric($withdrawOrder) && $orderStatus == 3) {
            $withdrawOrderInfo = DB::table('dcuex_user_withdraw_order as w_order')
                ->join('dcuex_user_wallet as u_wallet', 'w_order.withdraw_currency_id', 'u_wallet.user_wallet_currency_id')
                ->where('w_order.id', $withdrawOrder)
                ->get(['withdraw_currency_id','withdraw_amount', 'user_wallet_balance', 'user_wallet_balance_freeze_amount'])
                ->first();

            $validBalance = $withdrawOrderInfo->user_wallet_balance - $withdrawOrderInfo->withdraw_amount >= $withdrawOrderInfo->user_wallet_balance_freeze_amount;
            if (!$validBalance) {

                return response()->json(['code'=>100070 ,'msg' => '钱包可用余额不足']);
            }
        }

        return $next($request);
    }
}
