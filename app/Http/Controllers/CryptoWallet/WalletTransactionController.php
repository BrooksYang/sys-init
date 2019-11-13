<?php

namespace App\Http\Controllers\CryptoWallet;

use App\Models\Currency;
use App\Models\Wallet\FinanceSubject;
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
        $filterSubject = trim($request->filterSubject,'');
        $filterStatus = trim($request->filterStatus,'');
        $filterType = trim($request->filterType ?: $filterType,'');
        $remark = trim($request->remark,'');
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
        $subject = FinanceSubject::all();

        $transDetails = WalletTransaction::with(['user','currency','subject'])
            ->when($filterSys , function ($query) use ($search){
                return $query->where('user_id',0);
            })
            ->when($filterMerchant, function ($query) use ($search){
                return $query->whereHas('user', function ($query) use ($search) {
                    $query->where('is_merchant', User::MERCHANT)
                        ->when($search, function ($query) use ($search){
                            $query->where('username', 'like', "%$search%")
                                ->orwhere('email', 'like', "%$search%")
                                ->orwhere('phone', 'like', "%$search%");
                        });
                });
            })
            ->when($filterUser, function ($query) use ($search){
                return $query->whereHas('user', function ($query) use ($search) {
                    $query->when($search, function ($query) use ($search) {
                        $query->where('username', 'like', "%$search%")
                            ->orwhere('email', 'like', "%$search%")
                            ->orwhere('phone', 'like', "%$search%");
                    });
                });
            })
            ->when($search && !$filterUser && !$filterSys && !$filterMerchant, function ($query) use ($search){
                // 兼容按“用户名”搜索运营方对外提币（运营方对外提币即user_id=0）
                if (str_contains('system', $search)) {
                    return $query->where('user_id',0);
                }

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
            ->when($filterSubject, function ($query) use ($filterSubject){
                return $query->where('subject_id', $filterSubject);
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
            ->when($remark, function ($query) use ($remark){
                return $query->where('remark', 'like', "%$remark%");
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

        $search = $search || $from || $to || $start || $end || $filterSys || $filterMerchant || $filterSubject;

        if ($search) {
            $statistics = $transDetails;
            $statistics = $this->statistics($statistics->get(['amount','type','fee'])->groupBy('type'));
        }

        $transDetails = $transDetails->paginate(config('app.pageSize'));

        return compact('status','type','withdrawType','currencies','transDetails',
            'search','statistics','external','subject');
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
        list($transDeposit,$transWithDraw, $transDepositFee, $transWithDrawFee) = [0,0,0,0];

        foreach ($transDetails as $key => &$userTransDetail) {
            //$userTransDetail = array_column($userTransDetail->toArray(), 'usdt_amount');
            $transDetail = $userTransDetail->pluck('amount');
            $transDetailFee = $userTransDetail->pluck('fee');

            switch ($key) {
                case WalletTransaction::DEPOSIT:
                    $transDeposit = $this->amountMap($transDetail);
                    $transDepositFee = $this->amountMap($transDetailFee);
                    break;
                case WalletTransaction::WITHDRAW:
                    $transWithDraw = $this->amountMap($transDetail);
                    $transWithDrawFee = $this->amountMap($transDetailFee);
                    break;
            }
        }

        $amountByType = compact('transDeposit','transDepositFee','transWithDraw','transWithDrawFee');

        $total = $this->amountMap(compact('transDeposit','transWithDraw'));

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
