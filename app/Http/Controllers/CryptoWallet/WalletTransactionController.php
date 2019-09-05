<?php

namespace App\Http\Controllers\CryptoWallet;

use App\Models\Currency;
use App\Models\Wallet\WalletExternal;
use App\Models\Wallet\WalletTransaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 用户数字钱包交易记录
 *
 * Class WalletTransactionController
 * @package App\Http\Controllers\CryptoWallet
 */
class WalletTransactionController extends Controller
{
    /**
     * 钱包交易记录
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $this->walletTransaction($request);

        return view('wallet.walletTransactionIndex', $data);
    }

    /**
     * 数据查询
     *
     * @param $request
     * @param string $filterSys
     * @param string $filterType
     * @return array
     */
    public function walletTransaction($request, $filterSys='', $filterType='')
    {
        // 搜索用户或钱包地址
        $search = trim($request->search,'');
        $from = trim($request->from,'');
        $to = trim($request->to,'');
        $filterWithdrawType = trim($request->filterWithdrawType ?:$filterSys,'');
        $filterCurrency = trim($request->filterCurrency,'');
        $filterStatus = trim($request->filterStatus,'');
        $filterType = trim($request->filterType ?: $filterType,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC,'') ?: 'desc';

        // 筛选提币类型 -系统-商户-普通用户
        $filterMerchant = $filterWithdrawType == WalletTransaction::MERCHANT_WITHDRAW ? true : false;
        $filterUser = $filterWithdrawType == WalletTransaction::USER_WITHDRAW ? true : false;
        $filterSys = $filterWithdrawType == WalletTransaction::SYS_WITHDRAW ? true : false;

        $status = WalletTransaction::STATUS;
        $type = WalletTransaction::TYPE;
        $withdrawType = WalletTransaction::WITHDRAW_TYPE;
        $currencies = Currency::getCurrencies();
        $external = WalletExternal::getAddr();

        $transDetails = WalletTransaction::with(['user','currency'])
            ->when($filterSys , function ($query) use ($search){
                return $query->where('user_id',0);
            })
            ->when($filterMerchant, function ($query) use ($search){
                return $query->whereHas('user', function ($query) use ($search) {
                    $query->where('is_merchant', User::MERCHANT)
                        ->where(function ($query) use ($search){
                            $query->where('username', 'like', "%$search%")
                                ->orwhere('email', 'like', "%$search%")
                                ->orwhere('phone', 'like', "%$search%");
                        });
                });
            })
            ->when($search && $filterUser, function ($query) use ($search){
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

        $search = $search || $from || $to || $start || $end || $filterSys || $filterMerchant;

        if ($search) {
            $statistics = $transDetails;
            $statistics = $this->statistics($statistics->get(['amount','type'])->groupBy('type'));
        }

        $transDetails = $transDetails->paginate(config('app.pageSize'));

        return compact('status','type','withdrawType','currencies','transDetails',
            'search','statistics','external');
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
        $transDeposit = $transWithDraw = 0;

        foreach ($transDetails as $key => &$userTransDetail) {
            //$userTransDetail = array_column($userTransDetail->toArray(), 'usdt_amount');
            $userTransDetail = array_column($userTransDetail->toArray(), 'amount');

            switch ($key) {
                case WalletTransaction::DEPOSIT:
                    $transDeposit = $this->amountMap($userTransDetail);
                    break;
                case WalletTransaction::WITHDRAW:
                    $transWithDraw = $this->amountMap($userTransDetail);
                    break;
            }
        }

        $amountByType = compact('transDeposit','transWithDraw');

        $total = $this->amountMap($amountByType);

        return array_add($amountByType, 'total', $total);
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
     * 系统提币记录
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sysWithdraw(Request $request)
    {
        // 提币的类型（系统、商户、用户） - 默认系统提取
        $filterSys = $request->filterWithdrawType ? false : WalletTransaction::SYS_WITHDRAW;

        // 记录类型（充值、提币） - 默认提币
        $filterType = $request->filterType ? false : WalletTransaction::WITHDRAW;

        $data = $this->walletTransaction($request, $filterSys, $filterType);

        return view('wallet.walletTransactionIndex', $data);
    }


}
