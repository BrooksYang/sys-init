<?php

namespace App\Http\Controllers\Order;

use App\Models\Currency;
use App\Models\OTC\OtcOrder;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

const OTC_ORDER_PAGE_SIZE = 30;
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

        // 团队红利结算状态
        $teamBonusStatus = OtcOrder::TEAM_BONUS_STATUS;

        // 币种
        $currencies = Currency::getCurrencies();

        // 系统商户
        $merchants = User::merchant();

        //按币种-用户名-电话-商户订单id检索
        $searchUser = trim($request->searchUser,'');
        $searchFromUser = trim($request->searchFromUser,'');
        $searchRemark = trim($request->searchRemark,'');
        $searchCardNumber = trim($request->searchCardNumber,'');
        $searchOtc = trim($request->searchOtc,'');
        $searchMerchant = trim($request->searchMerchant,'');
        $searchMerchantOrder = trim($request->searchMerchantOrder,'');
        $searchCurrency = trim($request->searchCurrency,'');
        $filterType = trim($request->filterType,'');
        $filterStatus = trim($request->filterStatus,'');
        $filterAppeal = trim($request->filterAppeal,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC ?: 'desc','');
        $uIds = [];

        if ($searchMerchant) {
            // 商户
            $merchant = User::find($searchMerchant);

            // 商户旗下用户id
            $uIds = $merchant->appKey->users()->pluck('id')->toArray();
        }


        $search = $searchUser || $searchOtc || $searchMerchant || $searchMerchantOrder || $filterStatus|| $filterAppeal ||  $start || $end;

        $userOtcOrder = OtcOrder::with(['user', 'tradeOwner','currency','legalCurrency'])
            ->when($searchUser, function ($query) use ($searchUser){
                $query->whereHas('user', function ($query) use ($searchUser) {
                    $query->where('username', 'like', "%$searchUser%")
                        ->orwhere('phone', 'like', "%$searchUser%")
                        ->orwhere('email', 'like', "%$searchUser%");
                });
            })
            ->when($searchFromUser, function ($query) use ($searchFromUser){
                $query->whereHas('tradeOwner', function ($query) use ($searchFromUser) {
                    $query->where('username', 'like', "%$searchFromUser%")
                        ->orwhere('phone', 'like', "%$searchFromUser%")
                        ->orwhere('email', 'like', "%$searchFromUser%");
                });
            })
            ->when($searchOtc, function ($query) use ($searchOtc){
                return $query->where('.id',  'like', "%$searchOtc%");
            })
            ->when($searchRemark, function ($query) use ($searchRemark){
                return $query->where('remark',  'like', "%$searchRemark%");
            })
            ->when($searchCardNumber, function ($query) use ($searchCardNumber){
                return $query->where('card_number',  'like', "%$searchCardNumber%");
            })
            ->when($searchMerchantOrder, function ($query) use ($searchMerchantOrder){
                return $query->where('merchant_order_id', 'like', "%$searchMerchantOrder%");
            })
            ->when($searchMerchant, function ($query) use ($searchMerchant, $uIds){
                return $query->whereIn('user_id', $uIds);
            })
            ->when($searchCurrency, function ($query) use ($searchCurrency){
                $query->whereHas('currency', function ($query) use ($searchCurrency) {
                    $query->where('id', $searchCurrency);
                });
            })
            ->when($start, function ($query) use ($start){
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                return $query->where('created_at', '<=', $end);
            })
            ->when($filterType, function ($query) use ($filterType){
                return $query->type($filterType);
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->status($filterStatus);
            })
            ->when($filterAppeal, function ($query) use ($filterAppeal){
                return $query->appealStatus($filterAppeal);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('created_at', $orderC);
            })
            ->get();

        $statistics = $this->sum($userOtcOrder);
        $userOtcOrder = self::selfPage($userOtcOrder, OTC_ORDER_PAGE_SIZE);

        return view('order.userOtcOrderIndex',compact('orderStatus', 'appealStatus', 'teamBonusStatus','currencies',
            'orderType','merchants','userOtcOrder','statistics','search'));
    }


    /**
     * 自定义分页
     *
     * @param $items
     * @param $perPage
     * @return LengthAwarePaginator
     */
    public static function selfPage($items, $perPage)
    {
        $pageStart = request('page', 1);
        $offSet    = ($pageStart * $perPage) - $perPage;
        // $itemsForCurrentPage = array_slice($items, $offSet, $perPage, TRUE);
        $itemsForCurrentPage = $items->slice($offSet, $perPage);
        return new LengthAwarePaginator( $itemsForCurrentPage, $items->count(), $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * 搜索统计
     *
     * @param $otcOrder
     * @return array
     */
    public function sum($otcOrder)
    {
        //bcscale(config('app.bcmath_scale'));

        list($totalFieldAmount, $totalCashAmount, $totalFee, $totalBonus)= [0, 0, 0, 0];

        foreach ($otcOrder ?? [] as $key => $item){
            $totalFieldAmount += $item->field_amount;
            $totalCashAmount += $item->cash_amount;
            $totalFee += $item->fee;
            $totalBonus += $item->team_bonus;
        }

        return compact('totalFieldAmount','totalCashAmount','totalFee','totalBonus');
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
