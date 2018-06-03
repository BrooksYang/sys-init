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
 * 交易 OTC 订单管理
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
        $orderStatus = [
            1 => ['name' => '已下单', 'class' => 'info'],
            2 => ['name' => '已支付', 'class' => 'primary'],
            3 => ['name' => '待放币', 'class' => 'warning'],
            4 => ['name' => '已放币', 'class' => 'success'],
            5 => ['name' => '已取消', 'class' => 'default']
        ];

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filterStatus = trim($request->filterStatus,'');
        $orderC = trim($request->orderC,'');
        $userOtcOrder = DB::table('otc_orders as otcOrder')
            ->join('users as u','otcOrder.user_id','u.id') //用户信息
            ->join('dcuex_crypto_currency as currency','otcOrder.currency_id','currency.id')  //币种
            ->join('otc_legal_currencies as legal_currency','otcOrder.legal_currency_id','legal_currency.id') //法币
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
                return $query->orderBy('otcOrder.created_at', 'desc'); //默认最后成交时间倒序
            })
            ->select(
                'otcOrder.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'legal_currency.name','legal_currency.abbr'
            )
            ->paginate(OTC_ORDER_PAGE_SIZE );;

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
        $orderStatus = [
            $request->field => $request->update,
            'updated_at' => gmdate('Y-m-d H:i:s',time()),
        ];
        $query = DB::table('otc_orders')->where('id', $id);

        //如核对通过则从交易用户的对应记账钱包中提币
        $jsonArray = ['code' =>0, 'msg' => '更新成功' ];
        if ($request->update == OTC_ORDER_STATUS_TRANSFER_OUT) {
            DB::transaction(function () use ($query, $orderStatus) {
                //更新提币订单
                $query->update($orderStatus);
                //获取订单信息
                $order = $query->get(['user_id' ,'currency_id', 'field_amount'])->first();
                //更新记账钱包余额
                DB::table('dcuex_user_wallet')
                    ->where('user_id' ,$order->user_id)
                    ->where('user_wallet_currency_id', $order->currency_id)
                    ->decrement('user_wallet_balance', $order->field_amount);
            });

            return response()->json($jsonArray);

        }elseif ($query->update($orderStatus)) {

            return response()->json($jsonArray);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 100090 ,'error' => '不能删除交易用户 OTC 订单']);

        /*if (DB::table('otc_orders')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
