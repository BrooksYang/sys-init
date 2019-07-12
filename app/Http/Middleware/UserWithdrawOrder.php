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
        return $next($request);
    }

}
