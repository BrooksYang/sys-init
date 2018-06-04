<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const USER_OTC_WITHDRAW_ORDER_PAGE_SIZE = 20;

/**
 * Class UserOtcWithdrawOrderController
 * @package App\Http\Controllers\Order
 * OTC 用户提币订单
 */
class UserOtcWithdrawOrderController extends Controller
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
            1 => ['name' => '待受理',  'class' => 'default'],
            2 => ['name' => '处理中',  'class' => 'primary'],
            3 => ['name' => '已发币',  'class' => 'success'],
            4 => ['name' => '失败',    'class' => 'danger'],
        ];

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filter = trim($request->filter,'');
        $orderC = trim($request->orderC,'');
        $userOtcWithdrawOrder = DB::table('otc_withdraws as withdraw')
            ->join('users as u','withdraw.user_id','u.id') //用户信息
            ->join('dcuex_crypto_currency as currency','withdraw.currency_id','currency.id')  //币种
            ->join('dcuex_user_crypto_wallet as u_wallet','withdraw.wallet_id','u_wallet.id') //用户真实钱包
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%");
            })
            ->when($filter, function ($query) use ($filter){
                return $query->where('withdraw.status', $filter);
            }, function ($query) {
                return $query->where('withdraw.status', 1); //默认过滤等待处理中
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('withdraw.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('withdraw.created_at', 'desc'); //默认创建时间倒序
            })
            ->select(
                'withdraw.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'u_wallet.crypto_wallet_title')
            ->paginate(USER_OTC_WITHDRAW_ORDER_PAGE_SIZE );

        return view('order.userOtcWithdrawOrderIndex', compact('orderStatus', 'userOtcWithdrawOrder'));
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
        $query = DB::table('otc_withdraws')->where('id', $id);

        //如核对通过则从交易用户的对应记账钱包中提币
        $jsonArray = ['code' =>0, 'msg' => '更新成功' ];
        if ($request->update == 3) {
            DB::transaction(function () use ($query, $orderStatus) {
                //更新提币订单
                $query->update($orderStatus);
                //获取订单信息
                $order = $query->get(['user_id' ,'currency_id', 'amount'])->first();
                //更新记账钱包余额
                DB::table('otc_balances')
                    ->where('user_id' ,$order->user_id)
                    ->where('currency_id', $order->currency_id)
                    ->decrement('available', $order->amount);
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
        return response()->json(['code' => 200050 ,'error' => '不能删除交易用户 OTC 提币订单']);

        /*if (DB::table('otc_withdraws')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
