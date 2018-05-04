<?php

namespace App\Http\Controllers\Issue;

use App\Http\Requests\CurrencyContractToUserRequeset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_CURRENCY_CONTRACT_PAGE_SIZE  = 20;

/**
 * Class CurrencyContractToUserController
 * @package App\Http\Controllers\Issue
 * 代币交易用户合约
 */
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
                ->orderBy('userCurrency.created_at','desc')
                ->paginate(USER_CURRENCY_CONTRACT_PAGE_SIZE);
        }else{
            $userCurrencyContract = $query->paginate(USER_CURRENCY_CONTRACT_PAGE_SIZE);
        }

        //获取并按币种整理交易对信息
        $symbolByCurrency = $this->symbolByCrrency();

        return view('issue.userCurrencyContractIndex',compact('userCurrencyContract', 'symbolByCurrency'));
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

        return view('issue.userCurrencyContractCreate',['currency' => $currency, 'symbolStr' =>'']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CurrencyContractToUserRequeset $request)
    {
        $currencyContract = $request->except(['_token', 'symbol', 'quote_currency', 'editFlag']);
        $currencyContract['created_at'] = gmdate('Y-m-d H:i:s',time());

        //获取并处理交易对
        $symbol = $this->sortOutSymbol($request->symbol, $request->currency_id, $request->quote_currency);

        DB::transaction(function () use ($symbol, $currencyContract) {
            DB::table('dcuex_currency_symbol')->insert($symbol);
            DB::table('dcuex_user_currency_contract')->insert($currencyContract);
        });

        return redirect('issuer/userCurrencyContract');
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
        $symbol = [];
        if ($userCurrencyContract->currency_id) {
            $currency = DB::table('dcuex_crypto_currency')->get(['id','currency_title_cn', 'currency_title_en_abbr']);
            //获取交易对信息
            $symbol = $this->getSymbol($userCurrencyContract->currency_id);
        }

        return view('issue.userCurrencyContractCreate',[
            'editFlag' => true,
            'userCurrencyContract' => $userCurrencyContract,
            'currency' => $currency,
            'symbol' => $symbol,
            'symbolStr' => implode(',',array_pluck($symbol->toArray(), 'quote_currency')),
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
        $userCurrencyContract = $request->except(['_token','_method','symbol','quote_currency','editFlag']);
        $currencyId = $request->currency_id;
        $query = DB::table('dcuex_user_currency_contract')->where('id',$id);
        $userCurrencyContract['updated_at'] = gmdate('Y-m-d H:i:s',time());

        //获取并处理交易对信息
        $symbol = $this->sortOutSymbol($request->symbol, $currencyId,$request->quote_currency);

        DB::transaction(function () use ($query, $userCurrencyContract, $currencyId, $symbol) {
            $query->update($userCurrencyContract);
            $querySymbol = DB::table('dcuex_currency_symbol');
            $querySymbol->where('currency_id', $currencyId)->delete();
            $querySymbol->insert($symbol);
        });


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
        return response()->json(['code' => 100010 ,'error' => '不能删除交易用户合约']);
        /*if (DB::table('dcuex_user_currency_contract')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }

    /**
     * 获取交易对信息
     *
     * @param $currencyId
     * @param string $sortOUt
     * @return \Illuminate\Support\Collection
     */
    public function getSymbol($currencyId, $sortOUt='')
    {
        return DB::table('dcuex_currency_symbol')
            ->where('currency_id', $currencyId)
            ->get(['quote_currency']);
    }

    /**
     * 整理交易对信息
     *
     * @param $requestSymbol
     * @param $currencyId
     * @param $quoteCurrency
     * @return array
     */
    public function sortOutSymbol($requestSymbol, $currencyId, $quoteCurrency)
    {
        $symbol = [];
        $quoteCurrency = strtolower($quoteCurrency);
        foreach ($requestSymbol as $key => $symbolItem) {
            $symbol[] = [
                'currency_id'=>$currencyId,
                'quote_currency'=>$symbolItem, //计价币种
                'symbol'=>$quoteCurrency.$symbolItem  //交易对
            ];
        }

        return $symbol;
    }

    /**
     * 按币种获取并整理交易对信息
     *
     * @return array
     */
    public function symbolByCrrency()
    {
        $currencySymbol = DB::table('dcuex_currency_symbol')->get(['currency_id', 'symbol']);
        $sortOutSymbol = [];
        foreach ($currencySymbol as $key => $symbol){
            $sortOutSymbol[$symbol->currency_id][] = $symbol->symbol;
        }

        return $sortOutSymbol;
    }
}
