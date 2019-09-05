<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Order\OtcOrderQuickController;
use App\Models\Currency;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\Wallet\WalletExternal;
use App\Models\Wallet\WalletTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * 系统收益列表
 *
 * Class OtcSysIncomeController
 * @package App\Http\Controllers\Wallet
 */
class OtcSysIncomeController extends Controller
{
    public $incomeType;

    public function __construct()
    {
        $this->incomeType = [
            1 => ['name' => '订单手续费',  'url'=>url('otc/sys/income')],
            2 => ['name' => '充值手续费',  'url'=>url('otc/sys/income?type=deposit')],
            3 => ['name' => 'OTC快捷购买', 'url'=>url('otc/sys/income?type=orderQuick')]
        ];
    }

    /**
     * OTC 订单交易手续费
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // OTC 充值手续费
        if ($request->type == 'deposit') {
            $depositIncome = $this->depositFee($request);
            return view('wallet.otcDepositFeeIncomeIndex', $depositIncome);
        }

        if ($request->type == 'orderQuick') {
            $request->filterStatus = OtcOrderQuick::RECEIVED;
            $orderQuickIncome = $this->orderQuickIncome($request);
            return view('wallet.otcQuickOrderIncomeIndex', $orderQuickIncome);
        }

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

        // OTC系统收益类型
        $incomeType = $this->incomeType;

        // 币种
        $currencies = Currency::getCurrencies();

        // 多条件搜索
        $searchUser = trim($request->searchUser,'');
        $searchFromUser = trim($request->searchFromUser,'');
        $searchRemark = trim($request->searchRemark,'');
        $searchCardNumber = trim($request->searchCardNumber,'');
        $searchOtc = trim($request->searchOtc,'');
        $searchMerchant = trim($request->searchMerchant,'');
        $searchCurrency = trim($request->searchCurrency,'');
        $filterType = trim($request->filterType,'');
        $filterStatus = trim($request->filterStatus ?: OtcOrder::RECEIVED,'');
        $filterAppeal = trim($request->filterAppeal,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC ?: 'desc','');

        $search = $searchUser || $searchOtc || $searchMerchant || $filterStatus|| $filterAppeal ||  $start || $end;

        $userOtcOrder = OtcOrder::with(['user','tradeOwner','currency','legalCurrency'])
            ->status($filterStatus)
            ->when($searchUser, function ($query) use ($searchUser){
                return $query->whereHas('user', function ($query) use ($searchUser){
                    return $query->where('username', 'like', "%$searchUser%")
                        ->orwhere('phone', 'like', "%$searchUser%")
                        ->orwhere('email', 'like', "%$searchUser%");
                });
            })
            ->when($searchFromUser, function ($query) use ($searchFromUser){
                return $query->whereHas('tradeOwner', function ($query) use ($searchFromUser){
                    return $query->where('username', 'like', "%$searchFromUser%")
                        ->orwhere('phone', 'like', "%$searchFromUser%")
                        ->orwhere('email', 'like', "%$searchFromUser%");
                });
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
            ->when($searchCurrency, function ($query) use ($searchCurrency){
                return $query->currency($searchCurrency);
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
            ->when($filterAppeal, function ($query) use ($filterAppeal){
                return $query->appealStatus($filterAppeal);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('created_at', $orderC);
            })
            ->get();

        $statistics = $this->sum($userOtcOrder);
        $userOtcOrder = self::selfPage($userOtcOrder, config('app.pageSize'));

        return view('wallet.otcOrderIncomeIndex',compact('orderStatus', 'appealStatus', 'currencies','orderType',
            'userOtcOrder','statistics','search','incomeType'));
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

        list($totalFieldAmount, $totalCashAmount, $totalFee)= [0, 0, 0];

        foreach ($otcOrder ?? [] as $key => $item){
            $totalFieldAmount += $item->field_amount;
            $totalCashAmount += $item->cash_amount;
            $totalFee += $item->fee;
        }

        return compact('totalFieldAmount','totalCashAmount','totalFee');
    }

    public function depositFee(Request $request)
    {
        // 多条件搜索
        $search = trim($request->search,'');
        $from = trim($request->from,'');
        $to = trim($request->to,'');
        $filterCurrency = trim($request->filterCurrency,'');
        $filterStatus = trim($request->filterStatus ?:WalletTransaction::SUCCESS,'');
        $filterType = trim($request->filterType ?:WalletTransaction::DEPOSIT,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC,'') ?: 'desc';

        $status = WalletTransaction::STATUS;
        $type = WalletTransaction::TYPE;
        $currencies = Currency::getCurrencies();
        $external = WalletExternal::getAddr();

        // OTC系统收益类型
        $incomeType = $this->incomeType;

        $transDetails = WalletTransaction::with(['user','currency'])
            ->when($search, function ($query) use ($search){
                return $query->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', "%$search%")
                        ->orwhere('email', 'like', "%$search%")
                        ->orwhere('phone', 'like', "%$search%");
                });
            })
            ->when($from, function ($query) use ($from){
                return $query->from($from);
            })
            ->when($to, function ($query) use ($to){
                return $query->to($to);
            })
            ->when($filterCurrency, function ($query) use ($filterCurrency){
                return $query->whereHas('currency', function ($query) use ($filterCurrency) {
                    return $query->where('id',$filterCurrency);
                });
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->status($filterStatus);
            })
            ->when($filterType, function ($query) use ($filterType){
                return $query->type($filterType);
            })
            ->when($start, function ($query) use ($start){
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                return $query->where('created_at', '<=', $end);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('created_at', $orderC);
            });
        //->paginate(self::WALLET_TRANS_PAGE_SIZE );

        if ($search) {
            $statistics = $transDetails;
            $statistics = $this->statistics($statistics->get(['amount','type','fee'])->groupBy('type'));
        }

        $transDetails = $transDetails->paginate(config('app.pageSize'));

        return  compact('status','type','currencies','transDetails',
            'search','statistics','external','incomeType');
    }

    /**
     * 交易记录数额统计
     *
     * @param $transDetails
     * @return int
     */
    public function statistics($transDetails)
    {
        bcscale(config('app.bcmath_scale'));
        list($transDeposit, $transWithDraw, $totalAmount, $depositFee,$withDrawFee,$totalFee)= [0,0,0,0,0,0];

        foreach ($transDetails as $key => &$userTransDetail) {
            //$userTransDetail = array_column($userTransDetail->toArray(), 'usdt_amount');
            $transDetail = array_column($userTransDetail->toArray(), 'amount');
            $userTransFee = array_column($userTransDetail->toArray(), 'fee');

            switch ($key) {
                case WalletTransaction::DEPOSIT:
                    $transDeposit = $this->amountMap($transDetail);
                    $depositFee = $this->amountMap($userTransFee);
                    break;
                case WalletTransaction::WITHDRAW:
                    $transWithDraw = $this->amountMap($transDetail);
                    $withDrawFee = $this->amountMap($userTransFee);
                    break;
            }
        }

        $amountByType = compact('transDeposit','transWithDraw','depositFee','withDrawFee');

        $totalAmount = $this->amountMap(compact('transDeposit','transWithDraw'));
        $totalFee = $this->amountMap(compact('depositFee','withDrawFee'));

        return array_merge($amountByType, ['totalAmount'=>$totalAmount, 'totalFee'=>$totalFee]);
    }

    /**
     * 遍历求和
     *
     * @param $amounts
     * @param int $sum
     * @return int|string
     */
    public function amountMap($amounts, $sum = 0)
    {
        bcscale(config('app.bcmath_scale'));

        foreach ($amounts as $key => $amount) {
            $sum = bcadd($sum, $amount);
        }
        return $sum;
    }

    /**
     * OTC 快捷抢单购买 - 溢价收益
     *
     * @param $request
     * @return array
     */
    public function orderQuickIncome($request)
    {
        $quickOrder = OtcOrderQuickController::quickOrder($request);

        return $quickOrder;
    }


}
