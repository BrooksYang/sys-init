<?php

namespace App\Http\Controllers\Issue;

use App\Http\Requests\CurrencyContractToUserRequeset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_CURRENCY_CONTRACT_PAGE_SIZE  = 20;

class CurrencyContractToUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $query = DB::table('dcuex_user_currency_contract as userCurrency')
            ->join('dcuex_crypto_currency as currency','userCurrency.currency_id','=','currency.id')
            ->select(['userCurrency.*', 'currency.currency_title_cn', 'currency.currency_title_en_abbr']);

        if ($search) {
            $userCurrencyContract = $query->where('currency.currency_title_cn','like',"%$search%")
                ->orwhere('currency.currency_title_en','like',"%$search%")
                ->paginate(USER_CURRENCY_CONTRACT_PAGE_SIZE);
        }else{
            $userCurrencyContract = $query->paginate(USER_CURRENCY_CONTRACT_PAGE_SIZE);
        }

        return view('issue.userCurrencyContractIndex',['userCurrencyContract' => $userCurrencyContract]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currency = DB::table('dcuex_crypto_currency')
            ->get(['id','currency_title_cn', 'currency_title_en','currency_title_en_abbr']);

        return view('issue.userCurrencyContractCreate',['currency' => $currency]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CurrencyContractToUserRequeset $request)
    {
        $currencyContract = $request->except(['_token','editFlag']);
        $currencyContract['created_at'] = gmdate('Y-m-d H:i:s',time());

        if (DB::table('dcuex_user_currency_contract')->insert($currencyContract)) {

            return redirect('issuer/userCurrencyContract');
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
        $userCurrencyContract = $currency = [];
        if ($id) {
            $userCurrencyContract = DB::table('dcuex_user_currency_contract as userCurrency')
                ->where('userCurrency.id',$id)->first();
        }
        if ($userCurrencyContract->currency_id) {
            $currency = DB::table('dcuex_crypto_currency')->get(['id','currency_title_cn', 'currency_title_en_abbr']);
        }

        return view('issue.userCurrencyContractCreate',[
            'editFlag' => true,
            'userCurrencyContract' => $userCurrencyContract,
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CurrencyContractToUserRequeset $request, $id)
    {
        $userCurrencyContract = $request->except(['_token','_method','editFlag']);
        $query = DB::table('dcuex_user_currency_contract')->where('id',$id);
        $userCurrencyContract['updated_at'] = gmdate('Y-m-d H:i:s',time());

        if ($query->first()) {
            $query->update($userCurrencyContract);
        }

        return redirect('issuer/userCurrencyContract');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (DB::table('dcuex_user_currency_contract')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
