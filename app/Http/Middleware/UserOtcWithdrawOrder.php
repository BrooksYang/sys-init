<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

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
        //订单状态：1 等待受理 2 处理中 3 已发币 4 失败,
        $method = $request->method();
        $withdrawOrder = last(explode('/',$request->path()) );
        $orderStatus = last(explode('=',$request->getQueryString()) );
        if (in_array($method,['PATCH','PUT']) && is_numeric($withdrawOrder) && $orderStatus == 3) {
            $withdrawOrderInfo = DB::table('otc_withdraws as w_order')
                ->join('otc_balances as u_wallet', 'w_order.currency_id', 'u_wallet.currency_id')
                ->where('w_order.id', $withdrawOrder)
                ->get(['w_order.currency_id', 'w_order.amount', 'available', 'frozen'])
                ->first();

            $validBalance = $withdrawOrderInfo->available - $withdrawOrderInfo->amount >= $withdrawOrderInfo->frozen;
            if (!$validBalance) {

                return response()->json(['code'=>200060 ,'msg' => '钱包可用余额不足']);
            }
        }

        return $next($request);
    }
}
