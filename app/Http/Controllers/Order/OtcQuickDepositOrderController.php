<?php

namespace App\Http\Controllers\Order;


use Illuminate\Http\Request;
use App\Models\OTC\OtcQuickDeposit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * OTC 快捷充值订单
 * Class OtcQuickDepositOrderController
 * @package App\Http\Controllers\Order
 */
class OtcQuickDepositOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //按用户信息检索
        $search = trim($request->search,'');
        $filterStatus = trim($request->filterStatus,'');
        $filterType = trim($request->filterType,'');
        $orderC = trim($request->orderC,'desc');

        $otcQuickDeposits = DB::table('otc_quick_deposits as otc')
            ->join('users as u','otc.user_id','u.id')
            ->when($search, function ($query) use ($search){
                return $query->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%")
                    ->orwhere('u.email', 'like', "%$search%");
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->where('otc.status', $filterStatus);
            })
            ->when($filterType, function ($query) use ($filterType){
                return $query->where('otc.pay_type', $filterType);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('otc.created_at', $orderC);
            })
            ->select(
                'otc.*', 'u.username', 'u.phone','u.email'
            )
            ->paginate(OtcQuickDeposit::OTC_QUICK_DEPOSITS_PAGE_SIZE);

        $orderStatus =  OtcQuickDeposit::STATUS;
        $payType =  OtcQuickDeposit::TYPE;

        return view('order.OtcQuickDepositOrderIndex',compact('otcQuickDeposits','orderStatus','payType','search'));
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
