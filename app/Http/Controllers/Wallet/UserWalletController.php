<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\UserWalletRequest;
use App\Models\Wallet\Balance;
use App\Models\Wallet\WalletsBalanceLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_WALLET_PAGE_SIZE = 20;

/**
 * Class UserWalletController
 * @package App\Http\Controllers\Wallet
 * 交易用户记账钱包
 */
class UserWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $userWallet = DB::table('wallet_balances as u_wallet')
            ->join('users as u','u_wallet.user_id','u.id')
            ->join('currencies as currency','u_wallet.user_wallet_currency_id','currency.id')
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('u.phone', 'like', "%$search%")
                    ->orwhere('u.email', 'like', "%$search%")
                    ->orwhere('u.username', 'like', "%$search%");
            })
            ->select('u_wallet.*', 'u.username','u.phone', 'u.email','currency.currency_title_cn','currency.currency_title_en_abbr')
            ->paginate(USER_WALLET_PAGE_SIZE );;

        return view('wallet.userWalletIndex',['userWallet' => $userWallet]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserWalletRequest $request)
    {
        $userWallet = $request->except(['_token','editFlag']);
        if (!empty($userWallet)) {
            DB::table('wallets')->insert($userWallet);
        }

        return redirect('user/wallet');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //获取币种信息
        $currency = DB::table('currencies')->get(['id', 'currency_title_cn', 'currency_title_en_abbr']);
        //获取用户记账钱包信息
        $userWallet = DB::table('wallet_balances as u_wallet')
            ->join('users as u','u_wallet.user_id','u.id')
            ->where('u_wallet.id',$id)
            ->select('u_wallet.*', 'u.username', 'u.email')
            ->first();

        return view('wallet.userWalletCreate', [
            'currency' => $currency,
            'userWallet' => $userWallet,
            'editFlag'=>true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserWalletRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UserWalletRequest $request, $id)
    {
        bcscale(config('app.bcmath_scale'));
        $action = $request->action == 'add' ? 'bcadd' : 'bcsub';

        $balance = Balance::lockForUpdate()->find($id);

        if ($action == 'bcsub' && $request->amount > $balance->user_wallet_balance) {
            return back()->withErrors(['amount' => '可用余额不足']);
        }

        DB::transaction(function () use ($request, $id, $action, $balance){

            // 更新余额
            $from = $balance->user_wallet_balance;
            $balance->user_wallet_balance = $action($balance->user_wallet_balance, $request->amount);
            $balance->save();
            $to = $balance->user_wallet_balance;

            // 变更记录
            WalletsBalanceLog::create([
               'user_id' => $balance->user_id,
               'currency_id' => $balance->user_wallet_currency_id,
               'amount' => $request->amount,
               'from' => $from,
               'to' => $to,
               'remark' => $request->remark
            ]);
        });

        return back();
    }

    /**
     * 冻结用户钱包可用资产
     *
     * @param Request $request
     * @param $wallet
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function frozen(Request $request, $wallet)
    {
        bcscale(config('app.bcmath_scale'));

        $balance =  Balance::lockForUpdate()->find($wallet);

        if ($request->amount > $balance->user_wallet_balance) {
            return back()->withErrors(['amount' => '可用余额不足']);
        }

        DB::transaction(function () use ($request, $balance) {

            // 冻结钱包余额
            $from = $balance->user_wallet_balance;

            $balance->user_wallet_balance = bcsub($balance->user_wallet_balance, $request->amount);
            $balance->user_wallet_balance_freeze_amount = bcadd($balance->user_wallet_balance_freeze_amount, $request->amount);
            $balance->save();

            $to = $balance->user_wallet_balance;

            // 冻结记录
            WalletsBalanceLog::create([
                'user_id' => $balance->user_id,
                'currency_id' => $balance->user_wallet_currency_id,
                'amount' => $request->amount,
                'from' => $from,
                'to' => $to,
                'type' => WalletsBalanceLog::FROZEN,
                'remark' => $request->remark
            ]);
        });

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 100060 ,'error' => '不能删除交易用户记账钱包']);

        /*if (DB::table('wallet_balances')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }

    /**
     * 用户钱包余额变更记录
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function balanceLog(Request $request)
    {
        $search = trim($request->search);
        $orderC = trim($request->orderC ?: 'desc');

        $type = WalletsBalanceLog::TYPE;

        $balanceLog = WalletsBalanceLog::with(['user'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search){
                    $query->where('username','like', "%$search%")
                        ->orWhere('phone','like', "%$search%")
                        ->orWhere('email','like', "%$search%");
                });
            })
            ->orderBy('created_at', $orderC)
            ->paginate(config('app.pageSize'));

        return view('wallet.walletBalanceLogIndex', compact('search','type','balanceLog'));
    }
}
