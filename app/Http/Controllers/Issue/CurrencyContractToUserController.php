<?php

namespace App\Http\Controllers\Issue;

use App\Http\Requests\CurrencyContractToUserRequest;
use App\Http\Requests\symbolFeeRequest;
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
        $symbolByCurrency = $this->symbolByCurrency();

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

        return view('issue.userCurrencyContractCreate',['currency' => $currency, 'symbolStr' =>'', 'editFlag' =>'']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CurrencyContractToUserRequest $request)
    {
        $currencyContract = $request->except(['_token', 'symbol', 'quote_currency', 'editFlag']);
        $currencyContract['created_at'] = gmdate('Y-m-d H:i:s',time());

        //获取并处理交易对
        $symbol = $this->sortOutSymbol($request->symbol, $request->currency_id, $request->quote_currency,'created_at');

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
            'symbolStr' => implode(',',$symbol),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CurrencyContractToUserRequest $request, $id)
    {
        $userCurrencyContract = $request->except(['_token','_method','symbol','quote_currency','editFlag']);
        $currencyId = $request->currency_id;
        $query = DB::table('dcuex_user_currency_contract')->where('id',$id);
        $userCurrencyContract['updated_at'] = gmdate('Y-m-d H:i:s',time());

        //获取并处理新-旧交易对信息
        $symbol = $this->sortOutSymbol($request->symbol, $currencyId,$request->quote_currency, 'updated_at');
        $oldSymbol = DB::table('dcuex_currency_symbol')->where('base_currency_id', $currencyId)
            ->get(['base_currency_id','quote_currency_id','symbol','maker_fee','taker_fee','created_at']);
        $symbol = $this->getSymbolFee($symbol, $oldSymbol);

        DB::transaction(function () use ($query, $userCurrencyContract, $currencyId, $symbol) {
            $query->update($userCurrencyContract);
            $querySymbol = DB::table('dcuex_currency_symbol');
            $querySymbol->where('base_currency_id', $currencyId)->delete();
            foreach ($symbol as $key => $itemSymbol) {
                $querySymbol->insert($itemSymbol);
            }
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
     * 获取交易对中基础币种所对应计价币种信息
     *
     * @param $currencyId
     * @param string $sortOUt
     * @return array
     */
    public function getSymbol($currencyId, $sortOUt='')
    {
         $quoteCurrencyIds = DB::table('dcuex_currency_symbol')
            ->where('base_currency_id', $currencyId)
            ->get(['quote_currency_id'])->toArray();

         $quoteCurrencySymbol =  DB::table('dcuex_crypto_currency')
            ->whereIn('id', array_pluck($quoteCurrencyIds,'quote_currency_id'))
            ->get(['currency_title_en_abbr'])->toArray();

        foreach ($quoteCurrencySymbol as $key=>$item) {
            $quoteCurrencySymbol[$key] = strtolower($item->currency_title_en_abbr);
        }

        return $quoteCurrencySymbol;
    }

    /**
     * 整理交易对信息
     *
     * @param $requestSymbol
     * @param $currencyId
     * @param $quoteCurrency
     * @return array
     */
    public function sortOutSymbol($requestSymbol, $currencyId, $quoteCurrency, $action)
    {
        $symbol = $queryQuoteCurrency = [];
        $quoteCurrency = strtolower($quoteCurrency);
        foreach ($requestSymbol as $key => $symbolItem) {
            $queryQuoteCurrency[] = $symbolItem;
            $symbol[] = [
                'base_currency_id'=>$currencyId,
                //'quote_currency'=>$symbolItem, //计价币种字符
                'symbol'=>$quoteCurrency.$symbolItem  //交易对
            ];
        }
        //获取计价币种信息
        $quoteCurrencyInfo = DB::table('dcuex_crypto_currency')
            ->whereIn('currency_title_en_abbr' ,$queryQuoteCurrency)
            ->get(['id','currency_title_en_abbr'])->toArray();

        //遍历并整理基础币种-计价币种的交易对关系
        foreach ($symbol as $key => $itemSymbol) {
            foreach ($quoteCurrencyInfo as $flag => $itemQuoteCurrency ) {
                if (stristr($itemSymbol['symbol'], $itemQuoteCurrency->currency_title_en_abbr)) {
                    $symbol[$key]['quote_currency_id'] = $itemQuoteCurrency->id;
                    $symbol[$key][$action] = gmdate('Y-m-d H:i:s',time());
                }
            }
        }

        return $symbol;
    }

    /**
     * 按币种获取并整理交易对信息
     *
     * @return array
     */
    public function symbolByCurrency()
    {
        $currencySymbol = DB::table('dcuex_currency_symbol')
            ->get(['id','base_currency_id', 'symbol','quote_currency_id','maker_fee','taker_fee']);
        $sortOutSymbol = [];
        foreach ($currencySymbol as $key => $symbol){
            $sortOutSymbol[$symbol->base_currency_id][] = $symbol;
        }

        return $sortOutSymbol;
    }

    /**
     * 同步更新交易对费率
     *
     * @param symbolFeeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function symbolFeeUpdate(symbolFeeRequest $request)
    {
        $symbolFee = $request->except('_token');

        foreach ($symbolFee['symbolFee'] as $symbolId => $item) {
            DB::table('dcuex_currency_symbol')->where('id', $symbolId)
                ->update([
                    'maker_fee' => $item['maker_fee'],
                    'taker_fee' => $item['taker_fee'],
                    'updated_at' => gmdate('Y-m-d H:i:s',time())
                ]);
        }

        return redirect('issuer/userCurrencyContract')->with(['indexMsg'=>'更新成功']);
    }

    /**
     * 处理新旧交易对的费率信息
     *
     * @param $symbol
     * @param $oldSymbol
     * @return mixed
     */
    public function getSymbolFee($symbol, $oldSymbol)
    {
        foreach ($symbol as $key => &$value) {
            foreach ($oldSymbol as $flag => $oldValue) {
                if ($oldValue->symbol == $value['symbol']) {
                    $value['maker_fee'] = $oldValue->maker_fee;
                    $value['taker_fee'] = $oldValue->taker_fee;
                    $value['created_at'] = $oldValue->created_at;
                    $value['updated_at'] = gmdate('Y-m-d H:i:s',time());
                }
            }
            if (!isset($value['maker_fee']) || !isset($value['taker_fee'])) {
                $value['created_at'] = gmdate('Y-m-d H:i:s',time());
                if (isset($value['updated_at'])){ unset($value['updated_at']); }
            }
        }

        return $symbol;
    }
}
