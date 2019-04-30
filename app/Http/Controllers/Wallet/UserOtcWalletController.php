<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Requests\UserOtcWalletRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_OTC_WALLET_PAGE_SIZE = 20;

/**
 * Class UserOtcWalletController
 * @package App\Http\Controllers\OtcWallet
 * 交易用户 OTC 记账钱包
 */
class UserOtcWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $userOtcWallet = DB::table('otc_balances as u_wallet')
            ->join('users as u','u_wallet.user_id','u.id')
            ->join('dcuex_crypto_currency as currency','u_wallet.currency_id','currency.id')
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('u.phone', 'like', "%$search%")
                    ->orwhere('u.username', 'like', "%$search%");
            })
            ->select('u_wallet.*', 'u.username', 'u.email','currency.currency_title_cn','currency.currency_title_en_abbr')
            ->paginate(USER_OTC_WALLET_PAGE_SIZE );;

        return view('wallet.userOtcWalletIndex',['userOtcWallet' => $userOtcWallet]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //获取币种信息
       /* $currency = DB::table('dcuex_crypto_currency')->get(['id', 'currency_title_cn', 'currency_title_en_abbr']);

        return view('wallet.userOtcWalletCreate',['currency' => $currency]);*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserOtcWalletRequest $request)
    {
        $userOtcWallet = $request->except(['_token','editFlag']);
        if (!empty($userOtcWallet)) {
            DB::table('dcuex_user_crypto_wallet')->insert($userOtcWallet);
        }

        return redirect('otc/user/wallet');
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
        $currency = DB::table('dcuex_crypto_currency')->get(['id', 'currency_title_cn', 'currency_title_en_abbr']);
        //获取用户记账钱包信息
        $userOtcWallet = DB::table('otc_balances as u_wallet')
            ->join('users as u','u_wallet.user_id','u.id')
            ->where('u_wallet.id',$id)
            ->select('u_wallet.*', 'u.username', 'u.email')
            ->first();

        return view('wallet.userOtcWalletCreate', [
            'currency' => $currency,
            'userOtcWallet' => $userOtcWallet,
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
    public function update(UserOtcWalletRequest $request, $id)
    {
        $userOtcWallet = $request->except(['_token', '_method', 'editFlag']);
        $query = DB::table('otc_balances')->where('id',$id);
        if(!empty($userOtcWallet) && $query->first()){
            $query->update($userOtcWallet);
        }

        return redirect('otc/user/wallet');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 200010 ,'error' => '不能删除交易用户 OTC 记账钱包']);

        /*if (DB::table('otc_balances')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
