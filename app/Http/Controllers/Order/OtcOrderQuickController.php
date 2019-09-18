<?php

namespace App\Http\Controllers\Order;

use App\Models\OTC\OtcOrderQuick;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * OTC 出金-快捷抢单
 *
 * Class OtcOrderQuickController
 * @package App\Http\Controllers\Order
 */
class OtcOrderQuickController extends Controller
{
    /**
     * OTC 商户出金-币商快捷抢单列表
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $quickOrder = self::quickOrder($request);

        return view('order.otcQuickOrderIndex', $quickOrder);
    }

    /**
     * 快捷订单数据查询
     *
     * @param $request
     * @param $incomeType
     * @return array
     */
    public static function quickOrder($request, $incomeType = '')
    {
        // 多条件搜索
        $searchUser = trim($request->searchUser,''); // 币商
        $searchFromUser = trim($request->searchFromUser,''); // 发布者-商户旗下用户
        $searchMerchant = trim($request->searchMerchant,''); // 商户
        $searchRemark = trim($request->searchRemark,'');
        $searchCardNumber = trim($request->searchCardNumber,'');
        $searchOtc = trim($request->searchOtc,''); // 快捷订单号
        $searchMerchantOrder = trim($request->searchMerchantOrder,''); // 商户订单号

        $filterStatus = trim($request->filterStatus,'');
        $filterAppeal = trim($request->filterAppeal,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC ?: 'desc','');

        // 订单状态, 1已下单，2已支付，3确认收款(已发币)，4确认收币，5已取消
        $orderStatus = OtcOrderQuick::STATUS;

        // 申诉状态，1已申诉，2申诉处理中，3申诉完结
        $appealStatus = OtcOrderQuick::APPEAL_STATUS;

        $search = $searchUser || $searchOtc || $searchMerchant || $filterStatus|| $filterAppeal || $searchMerchantOrder|| $start || $end;

        $otcQuickOrder = OtcOrderQuick::with(['user','merchant'])
            ->when($searchUser, function ($query) use ($searchUser){
                return $query->whereHas('user', function ($query) use ($searchUser){
                    return $query->where('username', 'like', "%$searchUser%")
                        ->orwhere('phone', 'like', "%$searchUser%")
                        ->orwhere('email', 'like', "%$searchUser%");
                });
            })
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                return $query->whereHas('merchant', function ($query) use ($searchMerchant){
                    return $query->where('username', 'like', "%$searchMerchant%")
                        ->orwhere('phone', 'like', "%$searchMerchant%")
                        ->orwhere('email', 'like', "%$searchMerchant%");
                });
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->status($filterStatus);
            })
            ->when($filterAppeal, function ($query) use ($filterAppeal){
                return $query->appealStatus($filterAppeal);
            })
            ->when($searchFromUser, function ($query) use ($searchFromUser) {
                return $query->where('owner_phone', 'like', "%$searchFromUser%");
            })
            ->when($searchOtc, function ($query) use ($searchOtc){
                return $query->where('id',  'like', "%$searchOtc%");
            })
            ->when($searchRemark, function ($query) use ($searchRemark){
                return $query->where('remark',  'like', "%$searchRemark%");
            })
            ->when($searchCardNumber, function ($query) use ($searchCardNumber){
                return $query->where('card_number',  'like', "%$searchCardNumber%");
            })
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                return $query->where('merchant_order_id', 'like', "%$searchMerchant%");
            })
            ->when($start, function ($query) use ($start){
                return $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                return $query->where('updated_at', '<=', $end);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('updated_at', $orderC);
            })
            ->get();

        $statistics = self::sum($otcQuickOrder);
        $otcQuickOrder = self::selfPage($otcQuickOrder, config('app.pageSize'));

        return compact('orderStatus', 'appealStatus', 'otcQuickOrder','statistics','search', 'incomeType');
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
    public static function sum($otcOrder)
    {
        //bcscale(config('app.bcmath_scale'));
        list($totalFieldAmount, $totalIncome, $totalIncomeSys,$totalIncomeMerchant, $totalIncomeUser)= [0, 0, 0, 0, 0];

        foreach ($otcOrder ?? [] as $key => $item){
            $totalFieldAmount += $item->field_amount; // 累计交易数量
            $totalIncome += $item->income_total;  // 累计总收益
            $totalIncomeSys += $item->income_sys;  // 累计平台收益
            $totalIncomeMerchant += $item->income_merchant;  // 累计商户收益
            $totalIncomeUser += $item->income_user;  // 累计币商收益
        }

        return compact('totalFieldAmount','totalIncome','totalIncomeSys','totalIncomeMerchant','totalIncomeUser');
    }

}
