<?php

namespace App\Http\Controllers\OtcAd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const  OTC_AD_PAGE_SIZE = 20;

/**
 * Class OtcAdController
 * @package App\Http\Controllers\OtcAd
 * OTC 交易广告管理
 */
class OtcAdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderType = [
            1 => ['name' => '卖单', 'class' => 'info'],
            2 => ['name' => '买单', 'class' => 'primary']
        ];
        $receiptWay = [
            1 =>  ['name' => '银行卡',        'class' => ''],
            2 =>  ['name' => '支付宝',        'class' => ''],
            3 =>  ['name' => '微信',          'class' => ''],
            4 =>  ['name' => 'PayPal',       'class' => ''],
            5 =>  ['name' => '西联汇款',      'class' => ''],
            6 =>  ['name' => 'SWIFT',        'class' => ''],
            7 =>  ['name' => 'PayNow',       'class' => ''],
            8 =>  ['name' => 'Paytm',        'class' => ''],
            9 =>  ['name' => 'QIWI',         'class' => ''],
            10 => ['name' => 'e-Transter',   'class' => ''],
        ];

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filterReceiptWay = trim($request->filterReceiptWay,'');
        $orderC = trim($request->orderC,'');
        $otcAd = DB::table('otc_advertisements as otcAd')
            ->join('users as u','otcAd.user_id','u.id') //用户信息
            ->join('dcuex_crypto_currency as currency','otcAd.currency_id','currency.id')  //币种
            ->join('otc_legal_currencies as legal_currency','otcAd.legal_currency_id','legal_currency.id') //法币
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('legal_currency.name','like',"%$search%")
                    ->orwhere('legal_currency.abbr','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%");
            })
            ->when($filterReceiptWay, function ($query) use ($filterReceiptWay){
                return $query->where('otcAd.receipt_way', $filterReceiptWay);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('otcAd.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('otcAd.created_at', 'desc'); //默认创建时间倒序
            })
            ->select(
                'otcAd.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'legal_currency.name','legal_currency.abbr'
            )
            ->paginate(OTC_AD_PAGE_SIZE );

        return view('otcAd.otcAdIndex', compact('orderType', 'receiptWay','otcAd'));
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
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 100090 ,'error' => '不能删除交易用户 OTC 广告信息 ']);

        /*if (DB::table('otc_advertisements')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}