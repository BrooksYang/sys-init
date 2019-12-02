<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\OtcSysWithdrawRequest;
use App\Http\Requests\WalletExternalRequest;
use App\Models\Currency;
use App\Models\OTC\OtcConfig;
use App\Models\Wallet\FinanceSubject;
use App\Models\Wallet\WalletExternal;
use App\Models\Wallet\WalletTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * OTC 运营方提币地址管理
 *
 * Class OtcSysWithdrawController
 * @package App\Http\Controllers\Wallet
 */
class OtcSysWithdrawAddrController extends Controller
{
    /**
     * 外部地址列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 多条件搜索
        $search = trim($request->search,'');
        $filterStatus= trim($request->filterStatus,'');
        $filterType= trim($request->filterType,'');
        $orderC = trim($request->orderC ?:'desc','');

        $status = WalletExternal::STATUS;
        $type = WalletExternal::TYPE;

        $currencies = Currency::getCurrencies();
        $subject = FinanceSubject::all();

        $withdrawMin = OtcConfig::withdrawMin();
        $withdrawMax = OtcConfig::withdrawMax();

        $external = WalletExternal::with(['user'])
            ->when($search, function ($query) use ($search){
                return $query->whereHas('user', function ($query) use ($search) {
                    return $query->where('username','like', "%$search%")
                        ->orWhere('phone','like',"%$search%")
                        ->orWhere('email','like',"%$search%");
                });
            })
            ->when($filterStatus, function ($query) use ($filterStatus) {
                return $query->where('status', $filterStatus);
            })
            ->when($filterType, function ($query) use ($filterType) {
                return $query->where('type', $filterType);
            })
            ->when($orderC, function ($query) use ($orderC) {
                return $query->orderBy('created_at', $orderC);
            })
            ->paginate(config('app.pageSize'));

        return view('wallet.walletExternalIndex', compact('currencies','status','type','external','subject',
            'withdrawMin','withdrawMax'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param WalletExternalRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(WalletExternalRequest $request)
    {
        // 用户已存在被启用的地址
        if (WalletExternal::addrEnabled()) {
            return back()->withErrors(['address' => '已存在被启用的地址']);
        }

        WalletExternal::create([
            'user_id' => 0,
            'address' => $request->address,
            'desc' => $request->desc
        ]);

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WalletExternalRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(WalletExternalRequest $request, $id)
    {
        $external = WalletExternal::findOrFail($id);

        if ($request->address) {
            $external->address = $request->address;
        }

        if ($request->desc) {
            $external->desc = $request->desc;
        }

        $external->save();

        return back();
    }

    /**
     * 启用或停用外部地址
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request, $id)
    {
        $external =  WalletExternal::findOrFail($id);

        // 用户已存在被启用的地址
        if ($external->status==WalletExternal::DISABLE && WalletExternal::addrEnabled()) {
            return response()->json(['code' => 402, 'msg'=>'已存在被启用的地址']);
        }

        $external->status = $external->status == WalletExternal::ENABLE
            ? WalletExternal::DISABLE :WalletExternal::ENABLE;

        $external->save();

        return response()->json(['code' => 0, 'msg'=>'更新成功']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $external = WalletExternal::findOrFail($id);

        $external->delete();

        return response()->json([]);
    }

    /**
     * 发起提币申请
     *
     * @param OtcSysWithdrawRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function withdraw(OtcSysWithdrawRequest $request)
    {

        bcscale(config('app.bcmath_scale'));

        // TODO 运营方提币开关配置
        if (!config('conf.enable_sys_withdraw')) {
            return back()->withInput()->withErrors(['amount' => '系统暂未开启对外提币']);
        }

        // 是否满足系统提币限额（最低最高限额）
        if (bccomp($request->amount, OtcConfig::withdrawMin())==-1 || bccomp($request->amount, OtcConfig::withdrawMax()==1) ) {
            return back()->withInput()->withErrors(['amount' => '提币限额受限']);
        }

        // 判定可提币数量
        if ($request->amount > WalletExternal::available()) {
            return back()->withInput()->withErrors(['amount' => '可提数额不足']);
        }

        // 发起提币
        WalletTransaction::create([
            'user_id'     => 0,
            'currency_id' => $request->currency_id,
            'subject_id'  => $request->subject_id,
            'type'        => WalletTransaction::WITHDRAW,
            'amount'      => $request->amount,
            'to'          => $request->to,
            'remark'      => '提币-'.$request->remark,
        ]);

        $param = 'filterCurrency='.Currency::USDT.'&filterType='.WalletTransaction::WITHDRAW;

        return redirect("wallet/transaction?{$param}");
    }
}
