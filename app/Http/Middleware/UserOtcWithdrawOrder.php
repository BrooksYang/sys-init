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
        return $next($request);
    }

}
