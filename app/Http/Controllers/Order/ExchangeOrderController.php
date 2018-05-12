<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const ORDER_PAGE_SIZE = 20;

/**
 * Class ExchangeOrderController
 * @package App\Http\Controllers\Order
 * 交易订单
 */
class ExchangeOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //订单状态 1准备提交，2已提交，3部分成交，4部分成交撤销，5完全成交，6已撤销
        $status = [
            1 => ['name' => '准备提交' ,'class' => 'default'],
            2 => ['name' => '已提交' ,'class' => 'info'],
            3 => ['name' => '部分成交' ,'class' => 'primary'],
            4 => ['name' => '部分成交撤销' ,'class' => 'warning'],
            5 => ['name' => '完全成交' ,'class' => 'success'],
            6 => ['name' => '已撤销' ,'class' => 'danger']
        ];
        //订单类型 1市价买，2市价卖，3限价买，4限价卖',
        $type = [
            1 => ['name' => '市价买','class' => 'warning'],
            2 => ['name' => '市价卖','class' => 'success'],
            3 => ['name' => '限价买','class' => 'warning'],
            4 => ['name' => '限价卖','class' => 'success']
        ];
        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'');
        $filterType= trim($request->filterType,'');
        $filterStatus= trim($request->filterStatus,'');
        $order = DB::table('exchange_orders as order')
            ->join('users','order.user_id', 'users.id')
            ->when($filterType, function ($query) use ($filterType){
                return $query->where('order.type', $filterType);
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->where('order.status', $filterStatus);
            })
            ->when($search, function ($query) use ($search){
                return $query->where('order.base_currency','like',"%$search%")
                    ->orwhere('order.symbol', 'like', "%$search%")
                    ->orwhere('users.phone', 'like', "$search%")
                    ->orwhere('users.email', 'like', "$search%");
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->where('order.finished_at','>',0)->orderBy('order.finished_at', $orderC);
            }, function ($query) {
                return $query->orderBy('order.finished_at', 'desc'); //默认最后成交时间倒序
            })
            ->select('order.*','users.phone', 'users.email')
            ->paginate(ORDER_PAGE_SIZE);

        return view('order.exchangeOrderIndex', compact('status', 'type','order'));
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
        //
    }
}
