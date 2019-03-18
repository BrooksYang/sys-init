<?php

namespace App\Http\Controllers\CryptoWallet;

use App\Http\Requests\SysCryptoWalletReequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const SYS_CRYPTO_WALLET_PAGE_SIZE = 20;

/**
 * Class SysCryptoWalletController
 * @package App\Http\Controllers\CryptoWallet
 * 系统平台-运营方数字钱包
 */

class SysCryptoWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //钱包类型，1普通，2主钱包(用于归集，提币转账)
        $type = [
            0 => ['name' => '全部',   'class' => ''],
            1 => ['name' => '普通',   'class' => ''],
            2 => ['name' => '主钱包', 'class' => '']
        ];

        //支持按钱包名称和币种检索
        $search = trim($request->search,'');
        $filterType = trim($request->filterType,'');

        $sysCryptoWallet = DB::table('dcuex_sys_crypto_wallet as s_wallet')
            ->join('dcuex_crypto_currency as currency','s_wallet.sys_crypto_wallet_currency_id','currency.id')
            ->when($search, function ($query) use ($search){
                return $query->where('s_wallet.sys_crypto_wallet_title','like',"%$search%")
                    ->orwhere('currency.currency_title_cn', 'like', "%$search%")
                    ->orwhere('currency.currency_title_en_abbr', 'like', "%$search%");
            })
            ->when($filterType, function ($query) use ($filterType) {
                return $query->where('s_wallet.type', $filterType);
            })
            ->select('s_wallet.*','currency.currency_title_cn','currency.currency_title_en_abbr')
            ->paginate(SYS_CRYPTO_WALLET_PAGE_SIZE );;

        return view('cryptoWallet.sysCryptoWalletIndex', compact('sysCryptoWallet','type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //获取币种信息
        $currency = $this->getCurrecy();

        return view('cryptoWallet.sysCryptoWalletCreate',['currency' => $currency]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SysCryptoWalletReequest $request)
    {
        $userCryptoWallet = $request->except(['_token','editFlag']);
        $userCryptoWallet['created_at'] = self::carbonNow();

        if (DB::table('dcuex_sys_crypto_wallet')->insert($userCryptoWallet)) {

            return redirect('sys/cryptoWallet');
        }
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
        $currency = $this->getCurrecy();
        //获取系统平台数字钱包信息
        $sysCryptoWallet = DB::table('dcuex_sys_crypto_wallet as s_wallet')
            ->where('s_wallet.id',$id)->first();

        return view('cryptoWallet.sysCryptoWalletCreate', [
            'currency' => $currency,
            'sysCryptoWallet' => $sysCryptoWallet,
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
    public function update(SysCryptoWalletReequest $request, $id)
    {
        $sysCryptoWallet = $request->except(['_token', '_method', 'editFlag']);
        $sysCryptoWallet['updated_at'] = self::carbonNow();
        $query = DB::table('dcuex_sys_crypto_wallet')->where('id',$id);
        if($query->first()){
            $query->update($sysCryptoWallet);
        }

        return redirect('sys/cryptoWallet');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 100050 ,'error' => '不能删除系统平台数字钱包']);

        /*if (DB::table('dcuex_sys_crypto_wallet')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }

    /**
     * 获取币种信息
     * @return \Illuminate\Support\Collection
     */
    private function getCurrecy(){

        return DB::table('dcuex_crypto_currency')->get(['id', 'currency_title_cn', 'currency_title_en_abbr']);
    }
}
