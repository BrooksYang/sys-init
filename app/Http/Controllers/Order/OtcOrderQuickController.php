<?php

namespace App\Http\Controllers\Order;

use App\Models\OTC\OtcOrderQuick;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

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

        // 系统商户
        $merchants = User::merchant();

        $search = $searchUser || $searchOtc || $searchMerchant || $filterStatus|| $filterAppeal || $searchMerchantOrder|| $start || $end;

        $otcQuickOrder = OtcOrderQuick::with(['user'])
            ->when($searchUser, function ($query) use ($searchUser){
                return $query->whereHas('user', function ($query) use ($searchUser){
                    return $query->where('username', 'like', "%$searchUser%")
                        ->orwhere('phone', 'like', "%$searchUser%")
                        ->orwhere('email', 'like', "%$searchUser%");
                });
            })
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                return $query->where('merchant_id', $searchMerchant);
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->status($filterStatus);
            })
            ->when($filterAppeal, function ($query) use ($filterAppeal){
                return $query->appealStatus($filterAppeal);
            })
            ->when($filterAppeal==='0', function ($query) use ($filterAppeal){
                return $query->whereNull('appeal_status');
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
            ->when($searchMerchantOrder, function ($query) use ($searchMerchantOrder){
                return $query->where('merchant_order_id', 'like', "%$searchMerchantOrder%");
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

        return compact('orderStatus', 'appealStatus', 'merchants', 'otcQuickOrder','statistics','search', 'incomeType');
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
        list($totalFieldAmount, $totalIncome, $totalIncomeSys,$totalIncomeMerchant, $totalIncomeUser,$totalSubsidy)= [0,0,0,0,0,0];

        foreach ($otcOrder ?? [] as $key => $item){
            $totalFieldAmount += $item->field_amount; // 累计交易数量
            $totalIncome += $item->income_total;  // 累计总收益
            $totalIncomeSys += $item->income_sys;  // 累计平台收益
            $totalIncomeMerchant += $item->income_merchant;  // 累计商户收益
            $totalIncomeUser += $item->income_user;  // 累计币商收益
            $totalSubsidy += $item->subsidy;  // 累计商户向币商支付补贴
        }

        return compact('totalFieldAmount','totalIncome','totalIncomeSys','totalIncomeMerchant','totalIncomeUser','totalSubsidy');
    }

    /**
     * 出金 - 各币商完成情况统计
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function byTrader(Request $request)
    {
        $defStart = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $defEnd = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y'))-1);

        $searchUser = trim($request->searchUser,''); // 币商
        $searchMerchant = trim($request->searchMerchant,''); // 商户
        $start = trim($request->start ?:$defStart,'');
        $end = trim($request->end ?:$defEnd,'');

        if ($request->reset == 'all') {
            list($start, $end) = [null, null];
        }

        // 系统商户
        $merchants = User::merchant();

        // 总订单
        $data['total'] = $this->total($searchMerchant, $searchUser, $start, $end);

        // 待抢单
        $data['unGrab'] = $data['total']->filter(function ($item) {
            return $item->user_id == 0;
        })->pluck('orders','user_id');

        foreach ($data['total'] as $total) {
            if ($total->user_id) {
                // 待支付
                $data['unPay'][$total->user_id] = $this->unPay($searchMerchant, $searchUser, $start, $end)->pluck('orders','user_id');

                // 已完成
                $data['finished'][$total->user_id] = $this->finished($searchMerchant, $searchUser, $start, $end)->pluck('orders','user_id');

                // 处理中
                $data['appealing'][$total->user_id] = $this->appealing($searchMerchant, $searchUser, $start, $end)->pluck('orders','user_id');
            }
        }

        return view('order.otcQuickOrderStatistics', compact('merchants','start','end','data'));
    }

    /**
     * 各币商总计订单量（指定时间或全部）
     *
     * @param $searchMerchant
     * @param $searchUser
     * @param $start
     * @param $end
     * @return mixed
     */
    public function total($searchMerchant, $searchUser, $start, $end)
    {
        return OtcOrderQuick::with('user')
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                 $query->where('merchant_id', $searchMerchant);
            })
            ->when($searchUser, function ($query) use ($searchUser){
                $query->whereHas('user', function ($query) use ($searchUser) {
                    $query->where('username', 'like', "%$searchUser%");
                });
            })
            ->when($start, function ($query) use ($start){
                 $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                 $query->where('updated_at', '<=', $end);
            })
            ->groupBy('user_id')
            ->select('user_id',DB::raw('count(id) as orders'))
            ->paginate(config('app.pageSize'));
    }

    /**
     * 各币商未支付（全部或指定时间）
     *
     * @param $searchMerchant
     * @param $searchUser
     * @param $start
     * @param $end
     * @return mixed
     */
    public function unPay($searchMerchant, $searchUser, $start, $end)
    {
        return OtcOrderQuick::with('user')
            ->status(OtcOrderQuick::ORDERED)
            ->where('user_id','>',0)
            ->where(function ($query) {
                $query->whereNull('appeal_status')->orWhere('appeal_status',OtcOrderQuick::APPEAL_CANCELED);
            })
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                $query->where('merchant_id', $searchMerchant);
            })
            ->when($searchUser, function ($query) use ($searchUser){
                $query->whereHas('user', function ($query) use ($searchUser) {
                    $query->where('username', 'like', "%$searchUser%");
                });
            })
            ->when($start, function ($query) use ($start){
                 $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                 $query->where('updated_at', '<=', $end);
            })
            ->groupBy('user_id')
            ->select('user_id',DB::raw('count(id) as orders'))
            ->get();
    }

    /**
     * 各币商已完成订单（全部或指定时间）
     *
     * @param $searchMerchant
     * @param $searchUser
     * @param $start
     * @param $end
     * @return mixed
     */
    public function finished($searchMerchant, $searchUser, $start, $end)
    {
        return OtcOrderQuick::with('user')
            ->status(OtcOrderQuick::RECEIVED)
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                $query->where('merchant_id', $searchMerchant);
            })
            ->when($searchUser, function ($query) use ($searchUser){
                $query->whereHas('user', function ($query) use ($searchUser) {
                    $query->where('username', 'like', "%$searchUser%");
                });
            })
            ->when($start, function ($query) use ($start){
                 $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                 $query->where('updated_at', '<=', $end);
            })
            ->groupBy('user_id')
            ->select('user_id',DB::raw('count(id) as orders'))
            ->get();
    }

    /**
     * 各币商处理中订单（全部或指定时间）
     *
     * @param $searchMerchant
     * @param $searchUser
     * @param $start
     * @param $end
     * @return mixed
     */
    public function appealing($searchMerchant, $searchUser, $start, $end)
    {
        return OtcOrderQuick::with('user')
            ->status(OtcOrderQuick::ORDERED)
            ->where('appeal_status', '<', OtcOrderQuick::RELEASED)
            ->when($searchMerchant, function ($query) use ($searchMerchant){
                $query->where('merchant_id', $searchMerchant);
            })
            ->when($searchUser, function ($query) use ($searchUser){
                $query->whereHas('user', function ($query) use ($searchUser) {
                    $query->where('username', 'like', "%$searchUser%");
                });
            })
            ->when($start, function ($query) use ($start){
                 $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                 $query->where('updated_at', '<=', $end);
            })
            ->groupBy('user_id')
            ->select('user_id',DB::raw('count(id) as orders'))
            ->get();
    }

}
