<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_DEPOSIT_ORDER_PAGE_SIZE = 20;
const DEPOSITS_PROCESSING = 1;
const DEPOSITS_SUCCESS = 2;
const DEPOSITS_FAIL = 3;
const DEPOSITS_RETURN_PROCESSING= 4;
const DEPOSITS_RETURNED = 5;
const DEPOSITS_RETURN_FAIL = 6;

/**
 * Class UserDepositOrderController
 * @package App\Http\Controllers\Order
 * 用户充值订单管理
 */
class UserDepositOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //订单状态
        $orderStatus = [
            1 => ['name' => '处理中' ,'class' => 'default'],
            2 => ['name' => '成功' ,'class' => 'success'],
            3 => ['name' => '失败' ,'class' => 'danger'],
            4 => ['name' => '退回处理中' ,'class' => 'primary'],
            5 => ['name' => '已退回' ,'class' => 'info'],
            6 => ['name' => '退回失败' ,'class' => 'warning'],
        ];

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filter = trim($request->filter,'');
        $orderC = trim($request->orderC,'');
        $userDepositOrder = DB::table('dcuex_user_deposit_order as order')
            ->join('users as u','order.user_id','u.id') //用户信息
            ->join('dcuex_crypto_currency as currency','order.currency_id','currency.id')  //币种
            ->join('dcuex_sys_crypto_wallet as s_wallet','order.deposit_sys_crypto_wallet_id','s_wallet.id') //运营方数字钱包
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%");
            })
            ->when($filter, function ($query) use ($filter){
                return $query->where('order.deposit_order_status', $filter);
            }, function ($query) {
                return $query->where('order.deposit_order_status', 1); //默认过滤等待处理中
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('order.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('order.created_at', 'desc'); //默认创建时间倒序
            })
            ->select(
                'order.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                's_wallet.sys_crypto_wallet_title')
            ->paginate(USER_DEPOSIT_ORDER_PAGE_SIZE );;

        return view('order.userDepositOrderIndex',compact('orderStatus', 'userDepositOrder'));
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
        $query = DB::table('dcuex_user_deposit_order')->where('id', $id);

        if (!$this->checkAction($id, $request->update)) {

            return response()->json(['code'=>100061 ,'msg' => '非法操作']);
        }

        //如凭证审核通过则向交易用户的对应记账钱包充值
        $jsonArray = ['code' =>0, 'msg' => '更新成功' ];
        if ($request->update == DEPOSITS_SUCCESS) {
            DB::transaction(function () use ($query, $orderStatus) {
                //更新充值订单
                $query->update($orderStatus);
                //获取订单信息
                $order = $query->get(['user_id' ,'currency_id', 'deposit_amount'])->first();
                //更新记账钱包余额
                DB::table('dcuex_user_wallet')
                    ->where('user_id' ,$order->user_id)
                    ->where('user_wallet_currency_id', $order->currency_id)
                    ->increment(
                        'user_wallet_balance', $order->deposit_amount,
                        ['updated_at' => gmdate('Y-m-d H:i:s',time()) ]
                    );

                DB::table('dcuex_sys_wallet')
                    ->where('sys_wallet_currency_id', $order->currency_id)
                    ->increment(
                        'sys_wallet_balance', $order->amount,
                        ['updated_at' => gmdate('Y-m-d H:i:s',time()) ]
                    );
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
        return response()->json(['code' => 100060 ,'error' => '不能删除交易用户充值订单']);

        /*if (DB::table('dcuex_user_deposit_order')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }

    /**
     * 检查订单的更新操作
     * @param $orderId
     * @param $orderStatus
     * @return bool
     */
    public function checkAction($orderId, $orderStatus)
    {
        $orderSrcStatus = DB::table('dcuex_user_deposit_order as deposits')
            ->where('deposits.id', $orderId)->value('deposit_order_status');

        $actionStatus = in_array($orderStatus, [
            DEPOSITS_PROCESSING, DEPOSITS_FAIL, DEPOSITS_RETURN_PROCESSING,DEPOSITS_RETURNED,DEPOSITS_RETURN_FAIL]);

        if (($orderSrcStatus == DEPOSITS_SUCCESS) && $actionStatus) {
            return FALSE;
        }

        return TRUE;
    }
}
