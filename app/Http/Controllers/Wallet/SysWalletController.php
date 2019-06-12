<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\SysWalletRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const SYS_WALLET_PAGE_SIIZE = 20;

/**
 * Class SysWalletController
 * @package App\Http\Controllers\Wallet
 * 系统运营方记账钱包
 */
class SysWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $sysWallet = DB::table('wallets_balances_system as s_wallet')
            ->join('currencies as currency','s_wallet.sys_wallet_currency_id','currency.id')
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%");
            })
            ->select('s_wallet.*',  'currency.currency_title_cn', 'currency.currency_title_en_abbr')
            ->paginate(SYS_WALLET_PAGE_SIIZE );

        return view('wallet.sysWalletIndex',['sysWallet' => $sysWallet]);
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
    public function store(SysWalletRequest $request)
    {
        $sysWallet = $request->except(['_token','editFlag']);
        if (!empty($sysWallet)) {
            DB::table('wallets_balances_system')->insert($sysWallet);
        }

        return redirect('sys/wallet');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        ////获取币种信息
        $currency = DB::table('currencies')->get(['id', 'currency_title_cn', 'currency_title_en_abbr']);
        //获取系統平台记账钱包信息
        $sysWallet = DB::table('wallets_balances_system as s_wallet')
            ->where('sys_wallet_currency_id',$id)->first();

        return view('wallet.sysWalletCreate', [
            'currency' => $currency,
            'sysWallet' => $sysWallet,
            'editFlag'=>true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SysWalletRequest $request, $id)
    {
        $sysWallet = $request->except(['_token', '_method', 'editFlag']);
        $query = DB::table('wallets_balances_system')->where('id',$id);
        if(!empty($sysWallet) && $query->first()){
            $query->update($sysWallet);
        }

        return redirect('sys/wallet');
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

        /*if (DB::table('wallets_balances_system')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
