<?php

namespace App\Http\Controllers\CryptoWallet;

use App\Http\Requests\UserCryptoWalletRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_CRYPTO_WALLET_PAGE_SIZE = 20;

/**
 * Class UserCryptoWalletController
 * @package App\Http\Controllers\CryptoWallet
 * 交易用户数字钱包
 */

class UserCryptoWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = [
            0 => ['name' => '全部',   'class' => ''],
            1 => ['name' => '普通',   'class' => ''],
            2 => ['name' => '主钱包', 'class' => '']
        ];

        $search = trim($request->search,'');
        $filterType = trim($request->filterType,'');

        $userCryptoWallet = DB::table('wallets as u_wallet')
            ->join('users as u','u_wallet.user_id','u.id')
            ->join('currencies as currency','u_wallet.crypto_wallet_currency_id','currency.id')
            ->when($search, function ($query) use ($search){
                return $query->where('u_wallet.crypto_wallet_title','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%");
            })
            ->when($filterType, function ($query) use ($filterType) {
                return $query->where('u_wallet.type', $filterType);
            })
            ->select('u_wallet.*', 'u.username', 'u.email','currency.currency_title_cn','currency.currency_title_en_abbr')
            ->paginate(USER_CRYPTO_WALLET_PAGE_SIZE );;

        return view('cryptoWallet.userCryptoWalletIndex', compact('userCryptoWallet', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCryptoWalletRequest $request)
    {
        $userCryptoWallet = $request->except(['_token','editFlag']);
        if (!empty($userCryptoWallet)) {
            $userCryptoWallet['created_at'] = self::carbonNow();
            DB::table('wallets')->insert($userCryptoWallet);
        }

            return redirect('user/cryptoWallet');
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
        //获取用户数字钱包信息
        $userCryptoWallet = DB::table('wallets as u_wallet')
            ->join('users as u','u_wallet.user_id','u.id')
            ->where('u_wallet.id',$id)
            ->select('u_wallet.*', 'u.username', 'u.email')
            ->first();

        return view('cryptoWallet.userCryptoWalletCreate', [
            'currency' => $currency,
            'userCryptoWallet' => $userCryptoWallet,
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
    public function update(UserCryptoWalletRequest $request, $id)
    {
        $userCryptoWallet = $request->except(['_token', '_method', 'editFlag']);
        $userCryptoWallet['updated_at'] = self::carbonNow();
        $query = DB::table('wallets')->where('id',$id);
        if(!empty($userCryptoWallet) && $query->first() ){
            $query->update($userCryptoWallet);
        }

        return redirect('user/cryptoWallet');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 100050 ,'error' => '不能删除交易用户数字钱包']);

        /*if (DB::table('wallets')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
