<?php

namespace App\Http\Controllers\Wallet;

use App\Models\Currency;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\OTC\UserAppKey;
use App\Models\Wallet\WalletTransaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncomeTotalController extends Controller
{
    /**
     * OTC 系统总收益统计查询 - 按商户
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $search = trim($request->searchMerchant,''); // 搜索商户
        $start = trim($request->start,'');
        $end = trim($request->end,'');

        // 商户类型
        $merchantType = UserAppKey::TYPE;

        $searchMerchants = User::merchant();

        bcscale(config('app.bcmath_scale'));

        // 获取商户列表
        $merchants = User::with(['appKey:user_id,type'])->where('is_merchant', User::MERCHANTS)
            ->whereNotIn('id', [26])
            ->when($search, function ($query) use ($search) {
                $query->where('id', $search);
            })
            ->select('username','phone','email','id')
            ->paginate(config('app.pageSize'));

        // 充值提币手续费 - 与商户无关
        $transFee = WalletTransaction::whereIn('type', [WalletTransaction::DEPOSIT, WalletTransaction::WITHDRAW])
            ->status(WalletTransaction::SUCCESS)
            ->when($start, function ($query) use ($start) {
                $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('updated_at', '<=', $end);
            })
            ->sum('fee');

        // 商户贡献收益 - 入金/出金
        list($inTotal,$outTotal, $total) = [0,0,0];
        foreach ($merchants as $key => &$merchant) {
            $merchant->income = $this->income($merchant->id, $start, $end);
            $inTotal =  bcadd($inTotal, @$merchant->income['in']);
            $outTotal =  bcadd($outTotal, @$merchant->income['out']);
        }

        // 统计数据
        $total = bcadd($transFee, bcadd($inTotal, $outTotal));
        $statistics = compact('total', 'inTotal', 'outTotal', 'transFee');

        return view('wallet.sysIncomeTotalIndex', compact('merchants','merchantType', 'statistics','search','searchMerchants'));
    }

    /**
     * 商户入出金贡献收益
     *
     * @param $merchantId
     * @param $start
     * @param $end
     * @return array
     */
    public function income($merchantId, $start, $end)
    {
        $merchant = User::find($merchantId);

        // 商户旗下用户id
        $userIds = $merchant->appKey->users()->pluck('id')->toArray();

        // 入金贡献收益
        $in = OtcOrder::whereIn('user_id', $userIds)
            ->whereIn('type', [OtcOrder::BUY, OtcOrder::SELL])
            ->currency(Currency::USDT)
            ->status(OtcOrder::RECEIVED)
            ->when($start, function ($query) use ($start) {
                $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('updated_at', '<=', $end);
            })
            ->sum('fee');

        // 出金贡献收益
        $out = OtcOrderQuick::whereIn('owner_id', $userIds)
            ->status(OtcOrderQuick::RECEIVED)
            ->when($start, function ($query) use ($start) {
                $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('updated_at', '<=', $end);
            })
            ->sum('income_sys');

        return compact('in', 'out');
    }


}
