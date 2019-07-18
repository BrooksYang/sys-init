<?php

namespace App\Http\Controllers\Order;

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

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filterStatus = trim($request->filterStatus,'');
        $orderC = trim($request->orderC,'');
        $userOtcOrder = DB::table('otc_orders as otcOrder')
            ->join('users as u','otcOrder.user_id','u.id') //用户信息
            ->join('currencies as currency','otcOrder.currency_id','currency.id')  //币种
            ->join('legal_currencies as legal_currency','otcOrder.legal_currency_id','legal_currency.id') //法币
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('legal_currency.name','like',"%$search%")
                    ->orwhere('legal_currency.abbr','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%");
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->where('otcOrder.status', $filterStatus);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('otcOrder.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('otcOrder.created_at', 'desc'); //默认创建时间倒序
            })
            ->select(
                'otcOrder.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'legal_currency.name','legal_currency.abbr'
            )
            ->paginate(OTC_ORDER_PAGE_SIZE);

        return view('order.userOtcOrderIndex',compact('orderStatus', 'orderType','userOtcOrder'));
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
