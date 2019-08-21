<?php

namespace App\Http\Controllers\Order;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const OTC_ORDER_PAGE_SIZE = 20;
const OTC_ORDER_STATUS_TRANSFER_OUT =4;

/**
 * Class UserOtcOrderController
 * @package App\Http\Controllers\Order
 * OTC 交易订单管理
 */
class UserOtcOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderType = [
            1 => ['name' => '卖单', 'class' => 'info'],
            2 => ['name' => '买单', 'class' => 'primary']
        ];

        //1已下单，2已支付，3确认收款(已发币)，4确认收币，5已取消
        $orderStatus = [
            1 => ['name' => '已下单', 'class' => 'info'],
            2 => ['name' => '已支付', 'class' => 'primary'],
            3 => ['name' => '已发币', 'class' => 'warning'], // 3确认收款(已发币)
            4 => ['name' => '已完成', 'class' => 'success'], // 4确认收币
            5 => ['name' => '已取消', 'class' => 'default']
        ];

        // 申诉状态，1已申诉，2申诉处理中，3申诉完结
        $appealStatus = [
            1 => ['name' => '已申诉', 'class' => 'danger'],
            2 => ['name' => '处理中', 'class' => 'warning'],
            3 => ['name' => '已完结', 'class' => 'default'],
        ];

        // 币种
        $currencies = Currency::getCurrencies();

        //按币种-用户名-电话-商户订单id检索
        $searchUser = trim($request->searchUser,'');
        $searchOtc = trim($request->searchOtc,'');
        $searchMerchant = trim($request->searchMerchant,'');
        $searchCurrency = trim($request->searchCurrency,'');
        $filterStatus = trim($request->filterStatus,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC ?: 'desc','');

        $search = $searchUser || $searchOtc || $searchMerchant || $start || $end;

        $userOtcOrder = DB::table('otc_orders as otcOrder')
            ->join('users as u','otcOrder.user_id','u.id') //用户信息
            ->join('currencies as currency','otcOrder.currency_id','currency.id')  //币种
            ->join('legal_currencies as legal_currency','otcOrder.legal_currency_id','legal_currency.id') //法币
            ->when($searchUser, function ($query) use ($searchUser){
                return $query->where('u.username', 'like', "%$searchUser%")
                    ->orwhere('u.phone', 'like', "%$searchUser%")
                    ->orwhere('u.email', 'like', "%$searchUser%");
            })
            ->when($searchOtc, function ($query) use ($searchOtc){
                return $query->where('otcOrder.id',  'like', "%$searchOtc%");
            })
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                return $query->where('otcOrder.merchant_order_id', 'like', "%$searchMerchant%");
            })
            ->when($searchCurrency, function ($query) use ($searchCurrency){
                return $query->where('otcOrder.currency_id', $searchCurrency);
            })
            ->when($start, function ($query) use ($start){
                return $query->where('otcOrder.created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                return $query->where('otcOrder.created_at', '<=', $end);
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->where('otcOrder.status', $filterStatus);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('otcOrder.created_at', $orderC);
            })
            ->select(
                'otcOrder.*', 'u.username', 'u.phone','u.email',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'legal_currency.name','legal_currency.abbr'
            )
            ->paginate(OTC_ORDER_PAGE_SIZE);

        $statistics = $this->sum($userOtcOrder);

        return view('order.userOtcOrderIndex',compact('orderStatus', 'appealStatus', 'currencies','orderType',
            'userOtcOrder','statistics','search'));
    }

    /**
     * 搜索统计
     *
     * @param $otcOrder
     * @return array
     */
    public function sum($otcOrder)
    {
        $totalFieldAmount = $totalCashAmount = 0;

        foreach ($otcOrder ?? [] as $key => $item){
            $totalFieldAmount += bcadd($totalFieldAmount, $item->field_amount);
            $totalCashAmount += bcadd($totalCashAmount, $item->cash_amount);
        }

        return compact('totalFieldAmount','totalCashAmount');
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 200040 ,'error' => '不能删除交易用户 OTC 交易订单']);

        /*if (DB::table('otc_orders')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
