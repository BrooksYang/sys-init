<?php

namespace App\Http\Controllers\CryptoWallet;

use App\Models\Currency;
use App\Models\Wallet\WalletExternal;
use App\Models\Wallet\WalletTransaction;
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
        // 搜索用户或钱包地址
        $search = trim($request->search,'');
        $from = trim($request->from,'');
        $to = trim($request->to,'');
        $filterCurrency = trim($request->filterCurrency,'');
        $filterStatus = trim($request->filterStatus,'');
        $filterType = trim($request->filterType,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC,'') ?: 'desc';

        $status = WalletTransaction::STATUS;
        $type = WalletTransaction::TYPE;
        $currencies = Currency::getCurrencies();
        $external = WalletExternal::getAddr();

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
            $statistics = $this->statistics($statistics->get(['amount','type'])->groupBy('type'));
        }

        $transDetails = $transDetails->paginate(config('app.pageSize'));

        return view('wallet.walletTransactionIndex', compact('status','type','currencies','transDetails',
            'search','statistics','external'));
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


}
