<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\LegalCurrency;
use App\Models\NeuContract\Transaction;
use App\Models\NeuContract\Wallet;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\Wallet\Balance;
use App\Models\Wallet\WalletExternal;
use App\Models\Wallet\WalletTransaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

const EXCHANGE_ORDER_TYPE = [1=>'市价买', 2=>'市价卖', 3=>'限价买', 4=>'限价卖'];
const OTC_ORDER_STATUS = [1 => '已下单',2=>'已支付',3=>'已发币',4=>'已完成',5=>'已取消'];
const CACHE_LENGTH = 10;

/**
 * Class HomeController
 * @package App\Http\Controllers
 * Dashboard 统计数据展示
 *
 */
class HomeController extends Controller
{
    /**
     * 设置计算精度
     * HomeController constructor.
     */
    public function __construct()
    {
        bcscale(config('app.bcmath_scale'));
    }

    public function index()
    {
        $cacheLength = intval(config('app.cache_length'));
        $tc = $otc = $otcTicket = $rate = [];
        $rate = ['rate' => LegalCurrency::rmbRate()];

        //工单客服数据面板
        if (\Entrance::user()->role_id == config('app.supervisor_role_id')) {
            
            return view('ticketHome', $this->ticketStatisticItem($cacheLength));
        }

        //公共统计
        $public = $this->publicStatisticItem($cacheLength);

        // TC 统计
        if (env('APP_TC_MODULE')) {

            $tc = $this->tcStatisticItem($cacheLength);
        }

        // OTC 统计
        if (env('APP_OTC_MODULE')) {

            $otc = $this->otcStatisticItem($cacheLength);
        }

        return view('home', $public + $tc + $otc + $rate);
    }

    /**
     *
     * 客服工单统计项
     * @param $cacheLength
     * @return array
     */
    public function ticketStatisticItem($cacheLength)
    {
        //当天 OTC 系统未分配工单数量
        $sysTicketByNotAssign = Cache::remember('sysTicketByNotAssign', $cacheLength, function () {
            return $this->getSysTicketByState(1);
        });
        //当天 OTC 系统等待处理的工单数量
        $sysTicketByWaitingFor = Cache::remember('sysTicketByWaitingFor', $cacheLength, function () {
            return $this->getSysTicketByState(6);
        });
        //当天 OTC 客服-我的工单数量
        $myTicket = Cache::remember('myTicket', $cacheLength, function () {
            return $this->getSysTicketByState('', \Auth::id());
        });
        //当天 OTC 客服-我的待处理工单数量
        $myTicketByWaitingFor = Cache::remember('myTicketByWaitingFor', $cacheLength, function () {
            return $this->getSysTicketByState(2, \Auth::id());
        });
        // OTC 客服-我的工单数量--按处理状态
        $myTicketByStatus = Cache::remember('myTicketByStatus', $cacheLength, function () {
            return $this->getMyTicketByState();
        });

        return compact(
            'sysTicketByNotAssign',
            'sysTicketByWaitingFor',
            'myTicket',
            'myTicketByWaitingFor',
            'myTicketByStatus'
        );
    }

    /**
     *
     * 数据面板公共统计项
     * @param $cacheLength
     * @return array
     */
    public function publicStatisticItem($cacheLength)
    {
        //注册用户数
        $users = Cache::remember('users', $cacheLength, function () {
            return $this->getUser();
        });
        //最近7天注册用户数
        $lastSevenDayUser = Cache::remember('lastSevenDayUser', $cacheLength, function () {
            return $this->getLastSevenDayUser(7);
        });
        //用户账户状态
        $userAccountStatus = Cache::remember('userAccountStatus', $cacheLength, function () use ($users) {
            return $this->userAccountStatusStatistic($users);
        });
        //用户邮箱-手机验证状态统计
        $emailPhoneVerifyStatus = Cache::remember('emailPhoneVerifyStatus', $cacheLength, function () use ($users) {
            return $this->emailPhoneVerifyStatistic($users);
        });
        //谷歌人机验证状态
        $googleAuth =  Cache::remember('googleAuth', $cacheLength, function () use ($users){
            return $this->googleAuthStatistic($users);
        });
        //用户认证状态整体分布
        $userVerifyStatus = Cache::remember('userVerifyStatus', $cacheLength, function () use ($users){
            return $this->userVerifyStatus($users);
        });

        //币种信息
        $currency = Cache::remember('currency', $cacheLength, function () {
            return $this->getCurrency();
        });

        return compact(
            'users',
            'userAccountStatus',
            'emailPhoneVerifyStatus',
            'googleAuth',
            'userVerifyStatus',
            'currency',
            'lastSevenDayUser'
        );
    }

    /**
     *
     * TC 币币交易数据面板统计项
     * @param $cacheLength
     * @return array
     */
    public function tcStatisticItem($cacheLength)
    {
        //当天委托订单数
        $exchangeOrders = Cache::remember('exchangeOrders', $cacheLength, function () {
            return $this->getExchangeOrder();
        });
        //当天成交订单数
        $orderLogs = Cache::remember('orderLogs', $cacheLength , function () {
            return $this->getOrderLog();
        });
        //当天成交总额
        $orderAmount = Cache::remember('orderAmount', $cacheLength, function () {
            return $this->orderAmount();
        });

        //当天充值订单数量及金额-按处理状态区分
        $depositOrderStatus = Cache::remember('depositOrderStatus', $cacheLength, function () {
            return $this->getDepositOrder();
        });
        //当天提币订单数量及金额-按处理状态区分
        $withdrawOrderStatus = Cache::remember('withdrawOrderStatus', $cacheLength, function () {
            return $this->getWithdrawOrder();
        });
        //当天委托订单数量--按处理状态
        $exchangeOrderByStatus = Cache::remember('exchangeOrderByStatus', $cacheLength, function () {
            return $this->getExchangeByStatus();
        });
        //当天委托订单成交数量及金额--按类型
        $exchangeOrderByType = Cache::remember('exchangeOrderByType', $cacheLength, function () {
            return $this->getExchangeOrderByType();
        });
        //当天委托订单成交数量及价格 --按类型
        $exchangeOrderLog = Cache::remember('exchangeOrderLog', $cacheLength, function () {
            return $this->getExchangeOrderLog();
        });

        //提币订单状态
        //$withdrawOrderStatus = $this->withdrawOrderStatus();
        //提币订单类型
        //$orderType = $this->orderType();
        return compact(
            'exchangeOrders',
            'orderLogs',
            'orderAmount',
            'depositOrderStatus',
            'withdrawOrderStatus',
            'exchangeOrderByStatus',
            'exchangeOrderByType',
            'exchangeOrderLog'
        );
    }

    /**
     *  OTC 场外交易数据面板统计项
     *
     * @param $cacheLength
     * @return array
     * @throws
     */
    public function otcStatisticItem($cacheLength)
    {
        // 当天 OTC 订单成交数量及价格--按状态
        $otcOrder = Cache::remember('otcOrder', $cacheLength, function () {
            return $this->getOtcOrder();
        });

        // 当天 OTC 提币订单数量及金额-按处理状态区分
        $otcWithdrawOrderStatus = Cache::remember('otcWithdrawOrderStatus', $cacheLength, function () {
            return $this->getOtcWithdrawOrder();
        });

        // 当天 OTC 累计成功提现金额
        $grandOtcWithdrawOrder = Cache::remember('grandOtcWithdrawOrder', $cacheLength, function () {
            return $this->getOtcWithdrawOrderByS(3);
        });

        // OTC 累计成功充值数额 - 默认USDT
        $otcDepositAmount = Cache::remember('otcDepositAmount', $cacheLength, function () {
            return $this->getOtcTransactions(WalletTransaction::DEPOSIT);
        });

        // OTC 累计成功提币数额 - 默认USDT
        $otcWithdrawAmount = Cache::remember('otcWithdrawAmount', $cacheLength, function () {
            return $this->getOtcTransactions(WalletTransaction::WITHDRAW);
        });

        // OTC 系统待提币数额 - 默认USDT
        $otcTobeWithdraw = Cache::remember('otcTobeWithdraw', $cacheLength, function () {
            return $this->toBeWithdraw();
        });

        // OTC 系统待处理提币数额 - 默认USDT
        $otcToBeWithdrawPending = $this->toBeWithdrawPending(WalletTransaction::WITHDRAW);


        // OTC 系统提币地址余额 - 默认USDT
        $otcSysWithDrawAddrBalance = User::getSysWithDrawAddrBalance(config('blockChain.sys_withdraw_addr'));

        // OTC 系统储值地址余额 - 默认USDT
        $otcSysDepositAddrBalance = User::getSysDepositAddrBalance(config('blockChain.sys_deposit_addr'));


        // 结算平台系统待归集余额 - 默认USDT
        $neuCollectPending = Cache::remember('neuCollectPending', $cacheLength, function () {
            return $this->neuCollectPending();
        });

        // 结算平台系统归集账户余额 - 默认USDT
        $neuCollectionBalance = Cache::remember('neuCollectionBalance', $cacheLength, function () {
            return $this->neuCollectionBalance();
        });

        // OTC 累计买入成交数额及手续费 - 默认USDT 买入
        $otcBuyTotal = Cache::remember('otcBuyTotal', $cacheLength, function () {
            return $this->otcOrderTotal();
        });

        // OTC 累计卖出成交数额及手续费 - USDT 卖出
        $otcSellTotal = Cache::remember('otcSellTotal', $cacheLength, function () {
            return $this->otcOrderTotal(OtcOrder::SELL);
        });

        // OTC 快捷抢单-平台累计收益(商户出金-币商抢单买入) - USDT
        $otcQuickIncomeSys = Cache::remember('otcQuickIncomeSys', $cacheLength, function () {
            return $this->otcQuickIncomeSys();
        });

        // OTC 快捷抢单-平台累计收益-每天 - USDT
        $otcQuickIncomeSysOfDay = Cache::remember('otcQuickIncomeSysOfDay', $cacheLength, function () {
            return $this->otcQuickIncomeSysOfDay();
        });

        // OTC 订单买入及手续费统计-每天 - 默认USDT
        $otcBuyOfDay = Cache::remember('otcBuyOfDay', $cacheLength, function () {
            return $this->otcOrderOfDay(OtcOrder::BUY);
        });

        // OTC 订单卖出及手续费统计-每天 - 默认USDT
        $otcSellOfDay = Cache::remember('otcSellOfDay', $cacheLength, function () {
            return $this->otcOrderOfDay(OtcOrder::SELL);
        });

        // 钱包交易手续费-充值-每天  - 默认USDT
        $transFeeDepositOfDay = Cache::remember('transFeeDepositOfDay', $cacheLength, function () {
            return $this->walletTransFeeOfDay(WalletTransaction::DEPOSIT);
        });

        // 钱包交易手续费-充值  - 默认USDT
        $transFeeDeposit = Cache::remember('walletTransFeeDeposit', $cacheLength, function () {
            return $this->walletTransFee(WalletTransaction::DEPOSIT);
        });

        // 钱包交易手续费-提币 - 默认USDT
        $transFeeWithdraw = Cache::remember('walletTransFeeWithDraw', $cacheLength, function () {
            return $this->walletTransFee(WalletTransaction::WITHDRAW);
        });


        //  OTC 平台收益统计- 每天 默认USDT
        $otcSysIncomeOfDay = Cache::remember('otcSysIncomeOfDay', $cacheLength, function ()
            use ($otcBuyOfDay,$transFeeDepositOfDay,$otcQuickIncomeSysOfDay){
            return $this->sysFeeIncome($otcBuyOfDay, $transFeeDepositOfDay, $otcQuickIncomeSysOfDay);
        });

        // OTC 各商户贡献总收益统计 - 每天 默认USDT
        $incomeByMerchantOfDay = Cache::remember('incomeByMerchantOfDay', $cacheLength, function () {
            return $this->incomeByMerchantOfDay();
        });

        // OTC 各商户贡献总收益及出入金总额统计 -  默认USDT
        $incomeByMerchant = Cache::remember('incomeByMerchant', $cacheLength, function () {
            return $this->incomeByMerchant();
        });

        // OTC 各商户入金总额统计 - 每天 默认USDT
        $inAndOutByMerchantOfDay = Cache::remember('inAndOutByMerchantOfDay', $cacheLength, function () {
            return $this->inAndOutByMerchantOfDay();
        });
        $inByMerchantOfDay = Cache::remember('inByMerchantOfDay', $cacheLength, function () use ($inAndOutByMerchantOfDay){
            $inAndOutByMerchantOfDay['merchant']['today_buy_amount'] = 'today_buy_amount';
            return $inAndOutByMerchantOfDay;
        });

        // OTC 各商户入金贡献手续费总额统计 - 每天 默认USDT
        $feeByMerchantOfDay = Cache::remember('feeByMerchantOfDay', $cacheLength, function () use ($inAndOutByMerchantOfDay){
            $inAndOutByMerchantOfDay['merchant']['today_fee'] = 'today_fee';
            return $inAndOutByMerchantOfDay;
        });

        // OTC 平台累计提币（外部地址）
        $otcSysWithdraw = Cache::remember('otcSysWithdraw', $cacheLength, function () {
            return $this->otcSysIncomeWithdraw();
        });

        // OTC 平台累计交易手续费及溢价收益 - 收益（USDT）
        $otcOrderFee = bcadd($otcBuyTotal->fee, $otcSellTotal->fee);
        $walletFee = bcadd($transFeeDeposit, $transFeeWithdraw);
        $otcFeeTotal = bcadd($otcOrderFee, $walletFee);

        $otcSysIncomeTotal = bcadd($otcFeeTotal, $otcQuickIncomeSys); // 平台累计总收益=手续费收入+溢价收益
        $otcSysIncomeRmbTotal = bcmul($otcSysIncomeTotal, LegalCurrency::rmbRate() ?: 0);

        // OTC 平台当前收益 默认USDT
        $otcSysIncomeCurrent = Cache::remember('otcSysIncomeCurrent', $cacheLength, function () use ($otcSysIncomeTotal, $otcSysWithdraw) {
            return bcsub($otcSysIncomeTotal, $otcSysWithdraw);
        });
        $otcSysIncomeCurrentRmb = bcmul($otcSysIncomeCurrent, LegalCurrency::rmbRate() ?: 0);


        return compact(
            'otcOrder',
            'otcWithdrawOrderStatus',
            'grandOtcWithdrawOrder',
            'otcDepositAmount','otcWithdrawAmount','otcTobeWithdraw','neuCollectPending','neuCollectionBalance',
            'otcToBeWithdrawPending','otcSysWithDrawAddrBalance','otcSysDepositAddrBalance',
            'otcBuyTotal', 'otcSellTotal','otcBuyOfDay','otcSellOfDay',
            'transFeeDepositOfDay','transFeeDeposit', 'transFeeWithdraw', 'otcQuickIncomeSys','otcSysIncomeOfDay',
            'incomeByMerchantOfDay','incomeByMerchant', 'inByMerchantOfDay', 'feeByMerchantOfDay',
            'otcSysIncomeTotal','otcSysIncomeRmbTotal', 'otcSysIncomeCurrent','otcSysIncomeCurrentRmb'
        );
    }

    /**
     * OTC 平台收益统计 - 每天 默认USDT
     *
     * @param $otcBuyOfDay
     * @param $depositFeeOfDay
     * @param $otcQuickIncomeSysOfDay
     * @return array
     */
    public function sysFeeIncome($otcBuyOfDay, $depositFeeOfDay, $otcQuickIncomeSysOfDay)
    {
        bcscale(config('app.bcmath_scale'));
        $sysIncome = [];

        $otcBuy = $otcBuyOfDay->pluck('fee','time');
        $deposit = $depositFeeOfDay->pluck('fee','time');
        $quickIncome = $otcQuickIncomeSysOfDay->pluck('income','time');

        $otcBuyTime = $otcBuy->keys();
        $depositTime = $deposit->keys();
        $times = $otcBuyTime->merge($depositTime)->sort();

        foreach ($times as $time) {
            $sysIncome[$time]['otc_buy_fee'] = $otcBuy[$time] ?? 0;
            $sysIncome[$time]['deposit_fee'] = $deposit[$time] ?? 0;
            $sysIncome[$time]['quick_income'] = $quickIncome[$time] ?? 0;
            $sysIncome[$time]['total'] = bcadd(bcadd($otcBuy[$time] ?? 0, $deposit[$time] ?? 0), $quickIncome[$time] ?? 0);
        }

        return $sysIncome;
    }

    /**
     * OTC 运营方累计提币
     *
     * @param int $type
     * @return mixed
     */
    public static function otcSysIncomeWithdraw($type = WalletExternal::WITHDRAW_ADDR)
    {
         return WalletExternal::type($type)->sum('amount');
    }
    
    /**
     * 用户账户状态统计
     *
     * @param $users
     * @return array
     */
    public function userAccountStatusStatistic($users)
    {
        //用户状态
        $userStatus = $this->userStatus();
        //用户禁用状态
        $inValid = $this->getUser('is_valid',$userStatus['is_valid']['invalid']);
        $valid = $users - $inValid;

        return ['正常' => $valid, '禁用' => $inValid];
    }


    /**
     * 用户邮箱-手机验证状态统计
     *
     * @param $users
     * @return array
     */
    public function emailPhoneVerifyStatistic($users)
    {
        //用户状态
        $userStatus = $this->userStatus();
        //邮箱验证状态
        $emailNotVerify = $this->getUser('email_status', $userStatus['email_phone_status']['notVerify']);
        $emailVerified = $users - $emailNotVerify;

        //手机验证状态
        $phoneNotVerify = $this->getUser('phone_status', $userStatus['email_phone_status']['notVerify']);
        $phoneVerified = $users - $phoneNotVerify;

        $yAxis = ['邮箱验证','手机验证','注册用户'];
        $series = [
            '已验证' => [$emailVerified, $phoneVerified, $users],
            '未验证' => [$emailNotVerify, $phoneNotVerify]
        ];

        return compact('yAxis', 'series');
    }

    /**
     * Google Auth 验证状态统计
     *
     * @param $users
     * @return array
     */
    public function googleAuthStatistic($users)
    {
        //用户状态
        $userStatus = $this->userStatus();
        //Google Auth状态
        $googleAuthBindOn = $this->getUser('google_status', $userStatus['google_status']['bind_on']);
        $googleAuthBindOff = $this->getUser('google_status', $userStatus['google_status']['bind_off']);
        $googleAuthUnbind = $users - $googleAuthBindOn - $googleAuthBindOff;

        return ['已绑定' => $googleAuthBindOn, '绑定未开启' => $googleAuthBindOff, '未绑定' => $googleAuthUnbind];
    }


    /**
     * 用户认证状态整体分布图
     *
     * @param $users
     * @return array
     */
    public function userVerifyStatus($users)
    {
        $userStatus = $this->userStatus();
        //认证审核状态
        $pending = $this->getUser('verify_status',$userStatus['verify_status']['pending']);
        $notVerify = $this->getUser('verify_status',$userStatus['verify_status']['notVerify']);
        $fail= $this->getUser('verify_status',$userStatus['verify_status']['fail']);
        $verified = $users - $pending - $notVerify - $fail;

        $xAxis = ['待审核','未认证', '已认证', '认证失败'];
        $yAxis =[$pending, $notVerify, $verified, $fail];

        return compact('xAxis','yAxis');
    }

    /**
     * 按状态获取用户
     *
     * @param string $statusField
     * @param string $status
     * @return mixed
     */
    public static function getUser($statusField='', $status = '')
    {
        return DB::table('users')
            ->when($statusField, function ($query) use($statusField,$status) {
                return $query->where($statusField, $status);
            })->count();
    }


    /**
     * 用户状态
     *
     * @return array
     */
    public function userStatus()
    {
        return [
            'email_phone_status' => [
                'notVerify' => 1,  // ['name' => '未验证']
                'Verified' => 2     //['name' => '已验证']
            ],
            'google_status' => [
                'un_bind' => 1,     //['name' => '未绑定']
                'bind_off' => 2,    //['name' => '绑定未开启']
                'bind_on' => 3,     //['name' => '已开启'],
            ],
            'is_valid' => [
                'invalid' => 0,     //['name' => '禁用'],
                'valid' => 1        //['name' => '正常'],
            ],
            'verify_status' => [
                'notVerify' => 1,   // ['name' => '未认证']
                'pending' => 2,      //['name' => '待审核']
                'verified' => 3,     //['name' => '已认证']
                'fail' => 4          //['name' => '认证失败']
            ],
            'gender' => [
                'secret' => 0,       // ['name' => '保密']
                'male' => 1,         //['name' => '男']
                'female' =>2,        // ['name' => '女']
            ]
        ];
    }

    /**
     * 按状态获取委托订单
     *
     * @param string $statusField
     * @param string $status
     * @return mixed
     */
    public function getExchangeOrder($statusField='', $status = '')
    {
        return DB::table('orders')
            ->when($statusField, function ($query) use($statusField, $status){
                return $query->where($statusField, $status);
            })->where('created_at','like',env('APP_GMDATE', self::carbonNow()->toDateString()).'%')->count();
    }

    /**
     * 委托订单状态
     *
     * @return array
     */
    public function withdrawOrderStatusStatus()
    {
        return [
            'processing' => 1,          //['name' => '处理中']
            'success' => 2,             //['name' => '成功']
            'fail' => 3,                //['name' => '失败']
            'returnedInProgress' => 4,  //['name' => '退回处理中']
            'returned' => 5,            //['name' => '已退回'],
            'return_fail' => 6          //['name' => '退回失败']
        ];
    }

    /**
     * 按状态获取成交订单
     * @param string $statusField
     * @param string $status
     * @return mixed
     */

    public function getOrderLog($statusField='', $status = '')
    {
        return DB::table('order_logs')
            ->when($statusField, function ($query) use($statusField, $status){
                return $query->where($statusField, $status);
            })->where('created_at','like',env('APP_GMDATE',self::carbonNow()->toDateString()).'%')->count();
    }

    /**
     * 订单成交总额
     *
     * @return int|mixed
     */
    public function orderAmount()
    {
        $orders = DB::table('orders')
            ->where('created_at','like',env('APP_GMDATE',self::carbonNow()->toDateString()).'%')->get(['field_cash_amount']);
        $sumAmount = 0;
        foreach ($orders as $order => $amount) {
            $sumAmount += $amount->field_cash_amount;
        }

        return $sumAmount;
    }

    /**
     * 成交订单类型
     *
     * @return array
     */
    public function orderType()
    {
        return [
            'marketBuy' => 1,          //['name' => '市价买']
            'marketSell' => 2,         //['name' => '市价卖']
            'limitedBuy' => 3,         //['name' => '限价买']
            'limitedSell' => 4,        //['name' => '限价卖']
        ];
    }

    /**
     * 获取币种信息
     *
     * @return array
     */
    public function getCurrency()
    {
        $currency = DB::table('currencies')->get([
            'currency_title_en_abbr','currency_issue_amount','currency_issue_circulation'])->toArray();

        $currency['total'] = count($currency);
        $en = array_column($currency,'currency_title_en_abbr');
        $amount = array_column($currency,'currency_issue_amount');
        $circulation = array_column($currency,'currency_issue_circulation');
        $amountTotal = $circulationTotal = 0;
        foreach ($amount as $key => $item) {
            $amountTotal += $item;
            $circulationTotal += $circulation[$key];
        }

        return compact('en', 'amount','circulation','amountTotal','circulationTotal');
    }

    /**
     * 当天充值订单数量及金额-按处理状态区分
     *
     * @return mixed
     */
    public function getDepositOrder()
    {
        $query = DB::table('order_deposits');
        $depositOrder['order'] = $query->select(DB::raw("count(deposit_order_status) as orderNum"),
            DB::raw("sum(deposit_amount) as deposit_amount"),'deposit_order_status')
            ->where('created_at', 'like',env('APP_GMDATE', self::carbonNow()->toDateString()).'%')
            ->groupBy('deposit_order_status')->orderBy('deposit_order_status','desc')
            ->get();

        $depositOrderCount['order'] = [];
        foreach ($depositOrder['order'] as $key => $item) {
            $depositOrderCount['order'][$item->deposit_order_status]= $item;
        }
        $depositOrderCount['orderStatus'] = [6=>'退回失败',5=>'已退回',4=>'退回处理中',3=>'失败',2=>'成功',1=>'处理中'];

        return $depositOrderCount;
    }

    /**
     * 当天提币订单数量及金额-按处理状态区分
     *
     * @return mixed
     */
    public function getWithdrawOrder()
    {
        $query = DB::table('otc_withdraws');
        $withdrawOrder['order'] = $query->select(DB::raw("count(status) as orderNum"),
            DB::raw("sum(amount) as amount"),'status')
            ->where('created_at', 'like',env('APP_GMDATE', self::carbonNow()->toDateString()).'%')
            ->groupBy('status')->orderBy('status','asc')
            ->get();

        $withdrawOrderCount['order'] = [];
        foreach ($withdrawOrder['order'] as $key => $item) {
            $withdrawOrderCount['order'][$item->status] = $item;
        }
        $withdrawOrderCount['orderStatus'] = [1=>'等待受理', 2=>'处理中', 3=>'已发币', 4=>'失败'];
        $withdrawOrderArr = $withdrawOrder['order']->toArray();

        $withdrawOrderCount['maxAmount'] = max(array_column($withdrawOrderArr,'amount') ?: [0]);
        $withdrawOrderCount['amountInterval'] = ($withdrawOrderCount['maxAmount']/10)*2;
        $withdrawOrderCount['maxOrder'] = max(array_column($withdrawOrderArr,'orderNum') ?: [0]);
        $withdrawOrderCount['orderInterval'] = ($withdrawOrderCount['maxOrder']/10)*2;

        return $withdrawOrderCount;
    }

    /**
     * 当天委托交易订单数量--按状态区分
     *
     * @return array
     */
    public function getExchangeByStatus()
    {
        $orderStatus = DB::table('orders')->select(DB::raw('count(type) as statusNum'), 'status')
            ->where('created_at' ,'like', env('APP_GMDATE',self::carbonNow()->toDateString()).'%')
            ->groupBy('status')->orderBy('status', 'asc')->get();

        $status = [1=>'准备提交', 2=>'已提交', 3=>'部分成交',4=>'部分成交撤销',5=>'完全成交', 6=>'已撤销'];
        $order = [];
        foreach ($orderStatus as $key => $item) {
            $order[$item->status] = $item;
            $order[$item->status]->statusName = $status[$item->status];
        }

        return compact('orderStatus', 'order', 'status');
    }

    /**
     * 当天委托交易订单成交数量及金额-按类型区分
     *
     * @return mixed
     */
    public function getExchangeOrderByType()
    {
       $orderByType['order'] =  DB::table('orders')
            ->select(DB::raw('sum(field_amount) as amount'), 'type', DB::raw('sum(field_cash_amount) as cash_amount'))
            ->where('created_at' ,'like', env('APP_GMDATE',self::carbonNow()->toDateString()).'%')
            ->groupBy('type')->orderBy('type','asc')->get();

       $orderByTypeCount['order'] = [];
       foreach ($orderByType['order'] as $key => $item) {
           $orderByTypeCount['order'][$item->type] = $item;
       }
       $orderByTypeCount['type'] = EXCHANGE_ORDER_TYPE;
       $orderByTypeArr = $orderByType['order']->toArray();
       $orderByTypeCount['maxAmount'] = max(array_column($orderByTypeArr,'amount') ?: [0]);
       $orderByTypeCount['amountInterval'] = ($orderByTypeCount['maxAmount']/10)*2;
       $orderByTypeCount['maxCashAmount'] = max(array_column($orderByTypeArr,'cash_amount') ?: [0]);
       $orderByTypeCount['cashAmountInterval'] = ($orderByTypeCount['maxCashAmount']/10)*2;

        return $orderByTypeCount;
    }

    /**
     * 当天委托交易订单成交数量及价格-按类型区分
     *
     * @return mixed
     */
    public function getExchangeOrderLog()
    {
        $orderLog['order'] =  DB::table('order_logs')
            ->select(DB::raw('sum(field_amount) as amount'), 'type', DB::raw('sum(price) as price'))
            ->where('created_at' ,'like', env('APP_GMDATE',self::carbonNow()->toDateString()).'%')
            ->groupBy('type')->orderBy('type','asc')->get();

        $orderLogCount['order'] = [];
        foreach ($orderLog['order'] as $key => $item) {
            $orderLogCount['order'][$item->type] = $item;
        }
        $orderLogCount['type'] = EXCHANGE_ORDER_TYPE;
        $orderLogArr = $orderLog['order']->toArray();
        $orderLogCount['maxAmount'] = max(array_column($orderLogArr,'amount') ?: [0]);
        $orderLogCount['amountInterval'] = ($orderLogCount['maxAmount']/10)*2;
        $orderLogCount['maxPrice'] = max(array_column($orderLogArr,'price') ?: [0]);
        $orderLogCount['priceInterval'] = ($orderLogCount['maxPrice']/10)*2;

        return $orderLogCount;
    }

    /**
     * 最近7天注册用户数
     *
     * @param $day
     * @return int
     */
    public static function getLastSevenDayUser($day)
    {
        $timeStr = '-' . $day . ' day';
        $beginTime = Carbon::parse(Carbon::parse($timeStr)->toDateString())->toDateTimeString();
        $endTime = Carbon::create(null,null,null,23,59,59);
        return DB::table('users')
            ->where('created_at','>=',$beginTime)->where('created_at', '<=',$endTime)
            ->count();
    }

    /**
     * 当天 OTC 交易订单信息
     *
     * @return mixed
     *
     */
    public function getOtcOrder()
    {
        $otcOrder['order'] =  DB::table('otc_orders')
            ->select(DB::raw('sum(field_amount) as amount'), 'status', DB::raw('avg(price) as price'))
            ->where('created_at' ,'like', env('APP_GMDATE',self::carbonNow()->toDateString()).'%')
            ->groupBy('status')->orderBy('status','asc')->get();

        $otcOrderCount['order'] = [];
        foreach ($otcOrder['order'] as $key => $item) {
            $otcOrderCount['order'][$item->status] = $item;
        }
        $otcOrderCount['status'] = OTC_ORDER_STATUS;
        $otcOrderArr = $otcOrder['order']->toArray();
        $otcOrderCount['maxAmount'] = max(array_column($otcOrderArr,'amount') ?: [0]);
        $otcOrderCount['amountInterval'] = ($otcOrderCount['maxAmount']/10)*2;
        $otcOrderCount['maxPrice'] = max(array_column($otcOrderArr,'price') ?: [0]);
        $otcOrderCount['priceInterval'] = ($otcOrderCount['maxPrice']/10)*2;

        return $otcOrderCount;
    }


    /**
     * 当天 OTC 提币订单数量及金额-按处理状态区分
     *
     * @return mixed
     */
    public function getOtcWithdrawOrder()
    {
        $query = DB::table('otc_withdraws');
        $otcWithdrawOrder['order'] = $query->select(DB::raw("count(status) as orderNum"),
            DB::raw("sum(amount) as amount"),'status')
            ->where('created_at', 'like',env('APP_GMDATE', self::carbonNow()->toDateString()).'%')
            ->groupBy('status')->orderBy('status','asc')
            ->get();

        $otcWithdrawOrderCount['order'] = [];
        foreach ($otcWithdrawOrder['order'] as $key => $item) {
            $otcWithdrawOrderCount['order'][$item->status] = $item;
        }
        $otcWithdrawOrderCount['orderStatus'] = [1=>'等待受理', 2=>'处理中', 3=>'已发币', 4=>'失败'];
        $otcWithdrawOrderArr = $otcWithdrawOrder['order']->toArray();

        $otcWithdrawOrderCount['maxAmount'] = max(array_column($otcWithdrawOrderArr,'amount') ?: [0]);
        $otcWithdrawOrderCount['amountInterval'] = ($otcWithdrawOrderCount['maxAmount']/10)*2;
        $otcWithdrawOrderCount['maxOrder'] = max(array_column($otcWithdrawOrderArr,'orderNum') ?: [0]);
        $otcWithdrawOrderCount['orderInterval'] = ($otcWithdrawOrderCount['maxOrder']/10)*2;

        return $otcWithdrawOrderCount;
    }


    /**
     *  OTC 累计提现金额 - 默认USDT
     *
     * @param $status
     * @param $currency
     * @return int|mixed
     */
    public function getOtcWithdrawOrderByS($status, $currency = Currency::USDT)
    {
        $otcWithdrawOrder = DB::table('otc_withdraws')
            //->where('created_at', 'like',env('APP_GMDATE', self::carbonNow()->toDateString()).'%')
            ->where('currency_id', $currency)
            ->where('status', $status)
            ->get(['amount']);

        $grandOtcWithdrawOrder = 0;
        foreach ($otcWithdrawOrder as $key => $item){
            $grandOtcWithdrawOrder += $item->amount;
        }

        return $grandOtcWithdrawOrder;
    }

    /**
     *  OTC 累计成功充值/提币数额 - 默认USDT
     *
     * @param $type
     * @param $currency
     * @param $status
     * @return int|mixed
     */
    public static function getOtcTransactions($type, $currency = Currency::USDT, $status = WalletTransaction::SUCCESS)
    {
        $getOtcTransactions = DB::table('wallet_transactions')
            ->where('type', $type)
            ->where('currency_id', $currency)
            ->where('status', $status)
            ->get(['amount']);

        $transactions = 0;
        foreach ($getOtcTransactions as $key => $item){
            $transactions = bcadd($transactions, $item->amount, config('app.bcmath_scale'));
        }

        return $transactions;
    }

    /**
     * OTC 系统待提币数额 - 默认USDT
     *
     * @param int $currency
     * @return mixed
     */
    public function toBeWithdraw($currency = Currency::USDT)
    {
        return Balance::where('user_wallet_currency_id', $currency)->sum('user_wallet_balance');
    }

    /**
     * OTC 系统提币待处理数额 - 默认USDT
     *
     * @param int $type
     * @param int $currency
     * @return mixed
     */
    public function toBeWithdrawPending($type, $currency = Currency::USDT)
    {
        return  WalletTransaction::type($type)
            ->currency($currency)
            ->status(WalletTransaction::PENDING)
            ->sum('amount');
    }

    /**
     * 结算平台系统待归集余额
     *
     * @return mixed
     */
    public function neuCollectPending()
    {
        return Transaction::sysCollectPending();
    }

    /**
     * 结算平台系统归集账户余额
     *
     * @return mixed
     */
    public function neuCollectionBalance()
    {
        return Wallet::sysCollectionBalance();
    }

    /**
     * OTC 订单交易统计 - 默认USDT
     *
     * @param int $type
     * @param int $currency
     * @param int $status
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function otcOrderTotal($type = OtcOrder::BUY, $currency = Currency::USDT, $status = OtcOrder::RECEIVED)
    {
        $otcOrder =  DB::table('otc_orders')
            ->select(DB::raw('sum(field_amount) as field_amount'),DB::raw('sum(fee) as fee'))
            ->where('type', $type)
            ->where('currency_id', $currency)
            ->where('status', $status)
            ->first();

        return $otcOrder;
    }

    /**
     * OTC 快捷抢单-平台累计收益(商户出金-币商抢单买入)
     *
     * @return mixed
     */
    public static function otcQuickIncomeSys()
    {
        return OtcOrderQuick::status(OtcOrderQuick::RECEIVED)->sum('income_sys');
    }

    /**
     * OTC 快捷抢单-平台累计收益 - 每天
     *
     * @param $owners
     * @return mixed
     */
    public function otcQuickIncomeSysOfDay($owners = null)
    {
        $otcQuickIncomeSysOfDay = OtcOrderQuick::status(OtcOrderQuick::RECEIVED)
            ->when(is_array($owners), function ($query) use ($owners){
                $query->whereIn('owner_id', $owners);
            })
            ->select(\DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d') as time,sum(income_sys) as income, sum(merchant_final_amount) as amount"))
            ->groupBy('time')
            ->get();

        return $otcQuickIncomeSysOfDay;
    }

    /**
     * OTC订单买入或卖出及手续费统计 - 默认USDT
     *
     * @param $type
     * @param $currency
     * @param $users
     * @return mixed
     */
    public function otcOrderOfDay($type, $currency = Currency::USDT, $users = null)
    {
        $otcOrderOfDay = OtcOrder::type($type)
            ->currency($currency)
            ->status(OtcOrder::RECEIVED)
            ->when(is_array($users), function ($query) use ($users){
                $query->whereIn('user_id', $users);
            })
            ->select(\DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as time,sum(field_amount) as amount,sum(fee) as fee"))
            ->groupBy('time')
            ->get();

        return $otcOrderOfDay;
    }

    /**
     * 钱包交易手续费-每天 - 默认USDT
     *
     * @param $type
     * @param $currency
     * @return mixed
     */
    public function walletTransFeeOfDay($type, $currency = Currency::USDT)
    {
        $walletTransFeeOfDay = WalletTransaction::type($type)
            ->currency($currency)
            ->status(WalletTransaction::SUCCESS)
            ->select(\DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d') as time,sum(amount) as amount,sum(fee) as fee"))
            ->groupBy('time')
            ->get();

        return $walletTransFeeOfDay;
    }

    /**
     * 钱包交易手续费
     *
     * @param $type
     * @param $currency
     * @return mixed
     */
    public static function walletTransFee($type, $currency = Currency::USDT)
    {
        $walletTransFee = WalletTransaction::type($type)
            ->currency($currency)
            ->status(WalletTransaction::SUCCESS)
            ->sum('fee');

        return $walletTransFee;
    }

    /**
     * 按状态获取客服或系统工单
     *
     * @param $state
     * @param string $supervisor
     * @return mixed
     */
    public function getSysTicketByState($state='', $supervisor='')
    {
        return DB::table('otc_ticket')
            ->when($supervisor, function ($query) {
                return $query->where('supervisor_id', \Auth::id());
            })
            ->when($state, function ($query) use($state){
                return $query->where('ticket_state', $state);
            })->count('id');
    }

    /**
     * 按状态获取客服-我的工单
     *
     * @return array
     */
    public function getMyTicketByState()
    {
        $orderStatus = DB::table('otc_ticket')
            ->select(DB::raw('count(ticket_state) as statusNum'), 'ticket_state as status')
            ->where('supervisor_id', Auth::id())
            ->groupBy('status')->orderBy('status', 'asc')->get();

        //工单状态 1 未分配 2 已分配 3 已回复 4 已关闭 5 正在处理 6等待处理',
        $status = $this->getSysTicketState(true);
        $order = [];
        foreach ($orderStatus as $key => $item) {
            $order[$item->status] = $item;
            $order[$item->status]->statusName = $status[$item->status];
        }

        return compact('orderStatus', 'order', 'status');
    }

    /**
     * 系统工单状态
     *
     * @param string $supervisor
     * @return array
     */
    public function getSysTicketState($supervisor='')
    {
        if ($supervisor) {
            return [2=>'已分配', 3=>'已回复',4=>'已关闭',5=>'正在处理', 6=>'等待处理'];
        }

        return [1=>'未分配',2=>'已分配', 3=>'已回复',4=>'已关闭',5=>'正在处理', 6=>'等待处理'];
    }

    /**
     * 获取商户及其旗下用户
     *
     * @return array
     */
    public function merchantUser()
    {
        $merchantUsers = [];
        $merchants = User::merchant();
        foreach ($merchants as $key => $merchant) {
            // 商户
            $find = User::find($merchant->id);

            // 商户旗下用户id
            $userIds = @$find->appKey->users()->pluck('id')->toArray();
            $merchantUsers[$merchant->id] = $userIds;
        }

        return $merchantUsers;
    }

    /**
     * 获取商户所贡献各项收益 - 每天
     *
     * @param $merchantUsers
     * @return array
     */
    public function merchantContribution($merchantUsers)
    {
        $merchantContribution = [];
        foreach ($merchantUsers as $merchantId => $users) {
            $merchantContribution[$merchantId]['otcOrderBuy'] = $this->otcOrderOfDay(OtcOrder::BUY,Currency::USDT, $users);
            $merchantContribution[$merchantId]['otcQuickIncomeSys'] = $this->otcQuickIncomeSysOfDay($users);

            $merchantContribution[$merchantId]['otcOrderBuyAmount'] = $merchantContribution[$merchantId]['otcOrderBuy'];
            $merchantContribution[$merchantId]['otcQuickAmount'] = $merchantContribution[$merchantId]['otcQuickIncomeSys'];
        }

        return $merchantContribution;
    }

    /**
     * 整合商户所贡献总收益 - 每天
     *
     * @param $merchantContribution
     * @return array
     */
    public function merchantContributions($merchantContribution)
    {
        bcscale(config('app.bcmath_scale'));
        $merchantContributions = [];

        // 遍历商户订单交易及快捷抢单所贡献收益
        foreach ($merchantContribution as $merchantId => $contribution) {

            $sysIncome = [];

            $otcBuy = $contribution['otcOrderBuy']->pluck('fee','time');
            $quickIncome = $contribution['otcQuickIncomeSys']->pluck('income','time');

            $otcBuyTime = $otcBuy->keys();
            $quickIncomeTime = $quickIncome->keys();
            $times = $otcBuyTime->merge($quickIncomeTime)->sort();

            // 按时间遍历并计算商户所贡献总收益
            foreach ($times as $time) {
                $sysIncome[$time]['otc_buy_fee'] = $otcBuy[$time] ?? 0;
                $sysIncome[$time]['quick_income'] = $quickIncome[$time] ?? 0;
                $sysIncome[$time]['total'] = bcadd($otcBuy[$time] ?? 0, $quickIncome[$time] ?? 0);
            }

            $merchantContributions[$merchantId] = $sysIncome;
        }

        return $merchantContributions;
    }

    /**
     * 商户出入金数额及贡献收益 - 每天
     *
     * @param $merchantContribution
     * @return array
     */
    public function merchantInAndOut($merchantContribution)
    {
        bcscale(config('app.bcmath_scale'));
        $merchantContributions = [];

        // 遍历商户订单交易及快捷抢单所贡献收益
        foreach ($merchantContribution as $merchantId => $contribution) {

            $sysIncome = [];

            $otcBuy = $contribution['otcOrderBuy']->pluck('fee','time');
            $quickIncome = $contribution['otcQuickIncomeSys']->pluck('income','time');

            $otcBuyAmount = $contribution['otcOrderBuyAmount']->pluck('amount','time');
            $quickAmount = $contribution['otcQuickAmount']->pluck('amount','time');

            $otcBuyTime = $otcBuy->keys();
            $quickIncomeTime = $quickIncome->keys();
            $times = $otcBuyTime->merge($quickIncomeTime)->sort();

            // 按时间遍历并计算商户所贡献总收益
            foreach ($times as $time) {
                $sysIncome[$time]['otc_buy_fee'] = $otcBuy[$time] ?? 0;
                $sysIncome[$time]['quick_income'] = $quickIncome[$time] ?? 0;
                $sysIncome[$time]['total_income'] = bcadd($otcBuy[$time] ?? 0, $quickIncome[$time] ?? 0);

                $sysIncome[$time]['otc_buy_amount'] = $otcBuyAmount[$time] ?? 0;
                $sysIncome[$time]['quick_amount'] = $quickAmount[$time] ?? 0;
            }

            $merchantContributions[$merchantId] = $sysIncome;
        }

        return $merchantContributions;
    }

    /**
     * 按时间合并处理各商户所贡献总收益 - 每天
     *
     * @return array
     */
    public function incomeByMerchantOfDay()
    {
        bcscale(config('app.bcmath_scale'));
        $incomeByMerchantOfDay = [];

        $merchantUser = $this->merchantUser();
        $merchantIds = array_keys($merchantUser ?? []);
        $merchantContribution = $this->merchantContribution($merchantUser);
        $merchantContributions = $this->merchantContributions($merchantContribution);

        // 按时间合并处理各商户所贡献收益
        $times= [];
        foreach ($merchantContributions as $merchantId => $contribution) {
            $times += array_keys($contribution);
        }

        foreach ($times as $time) {
            foreach ($merchantIds as $merchantId) {
                $merchant = User::find($merchantId);
                $merchant= $merchant->username ?: $merchant->phone ?: $merchant->email;
                $merchantInfo[] = $merchant;

                $incomeByMerchantOfDay['data'][$time][$merchant] = $merchantContributions[$merchantId][$time]['total'] ?? 0;
                $incomeByMerchantOfDay['data'][$time]['total'] = bcadd($incomeByMerchantOfDay['data'][$time][$merchant], $incomeByMerchantOfDay['data'][$time]['total']??0);
            }
        }


        // 处理商户信息
        foreach ($merchantIds as $merchantId) {
            $merchant = User::find($merchantId);
            $incomeByMerchantOfDay['merchant'][]= $merchant->username ?: $merchant->phone ?: $merchant->email;
        }
        $incomeByMerchantOfDay['merchant']['total']= 'total';

        return $incomeByMerchantOfDay;
    }

    /**
     * 按时间合并处理商户出入金数额及贡献收益 - 每天
     *
     * @return array
     */
    public function inAndOutByMerchantOfDay()
    {
        bcscale(config('app.bcmath_scale'));
        $inAndOutDaily = [];

        $merchantUser = $this->merchantUser();
        $merchantIds = array_keys($merchantUser ?? []);
        $contribution = $this->merchantContribution($merchantUser);
        $contributions = $this->merchantInAndOut($contribution);

        // 按时间合并处理各商户所贡献收益及商户入/出金
        $times= [];
        foreach ($contributions as $merchantId => $contribution) {
            $times += array_keys($contribution);
        }

        foreach ($times as $time) {
            foreach ($merchantIds as $merchantId) {
                $merchant = User::find($merchantId);
                $merchant= $merchant->username ?: $merchant->phone ?: $merchant->email;
                $merchantInfo[] = $merchant;

                // 商户贡献收益（入金手续费及出金系统收益）
                $inAndOutDaily['data'][$time][$merchant]['otc_buy_fee'] = $contributions[$merchantId][$time]['otc_buy_fee'] ?? 0;
                $inAndOutDaily['data'][$time][$merchant]['quick_income'] = $contributions[$merchantId][$time]['quick_income'] ?? 0;
                $inAndOutDaily['data'][$time][$merchant]['total_income'] = $contributions[$merchantId][$time]['total_income'] ?? 0;

                // 商户入金、出金
                $inAndOutDaily['data'][$time][$merchant]['otc_buy_amount'] = $contributions[$merchantId][$time]['otc_buy_amount'] ?? 0;
                $inAndOutDaily['data'][$time][$merchant]['quick_amount'] = $contributions[$merchantId][$time]['quick_amount'] ?? 0;

                // 当天入金手续费总额、出金系统收益总额、总收益
                $inAndOutDaily['data'][$time]['today_fee'] = bcadd($inAndOutDaily['data'][$time][$merchant]['otc_buy_fee'],$inAndOutDaily['data'][$time]['today_fee'] ?? 0);
                $inAndOutDaily['data'][$time]['today_quick_income'] = bcadd($inAndOutDaily['data'][$time][$merchant]['quick_income'],$inAndOutDaily['data'][$time]['today_quick_income'] ?? 0);
                $inAndOutDaily['data'][$time]['today_income'] = bcadd($inAndOutDaily['data'][$time][$merchant]['total_income'],$inAndOutDaily['data'][$time]['today_income'] ?? 0);

                // 当天入金-出金总额
                $inAndOutDaily['data'][$time]['today_buy_amount'] = bcadd($inAndOutDaily['data'][$time][$merchant]['otc_buy_amount'], $inAndOutDaily['data'][$time]['today_buy_amount'] ?? 0);
                $inAndOutDaily['data'][$time]['today_quick_amount'] = bcadd($inAndOutDaily['data'][$time][$merchant]['quick_amount'], $inAndOutDaily['data'][$time]['today_quick_amount'] ?? 0);
            }
        }


        // 处理商户信息
        foreach ($merchantIds as $merchantId) {
            $merchant = User::find($merchantId);
            $inAndOutDaily['merchant'][]= $merchant->username ?: $merchant->phone ?: $merchant->email;
        }
        ///$inAndOutDaily['merchant'][$outField]= $outField;

        return $inAndOutDaily;
    }

    /**
     * 各商户贡献收益及出入金总额
     *
     * @return mixed
     */
    public function incomeByMerchant()
    {
        $incomeByMerchant = [];
        $merchantUser = $this->merchantUser();

        // 交易订单手续费总额、溢价收益（快捷抢单）总额、入金总额、出金总额
        foreach ($merchantUser as $merchantId => $users) {
            $merchant = User::find($merchantId);
            $merchant = $merchant->username ?: $merchant->phone ?: $merchant->email;
            $incomeByMerchant['merchant'][] = $merchant;

            $merchantOrder = $this->merchantOrder($users);
            $merchantOrderQuick = $this->merchantOrderQuick($users);

            $incomeByMerchant['merchantData'][$merchant]['fee'] = $merchantOrder->sum('fee');
            $incomeByMerchant['merchantData'][$merchant]['income_sys'] = $merchantOrderQuick->sum('income_sys');
            $incomeByMerchant['merchantData'][$merchant]['field_amount_in'] = $merchantOrder->sum('field_amount');
            $incomeByMerchant['merchantData'][$merchant]['field_amount_out'] = $merchantOrderQuick->sum('field_amount');

            $incomeByMerchant['data']['fee'] = array_column($incomeByMerchant['merchantData'],'fee');
            $incomeByMerchant['data']['income_sys'] = array_column($incomeByMerchant['merchantData'],'income_sys');
            $incomeByMerchant['data']['field_amount_in'] = array_column($incomeByMerchant['merchantData'],'field_amount_in');
            $incomeByMerchant['data']['field_amount_out'] = array_column($incomeByMerchant['merchantData'],'field_amount_out');
        }

        return $incomeByMerchant;
    }


    /**
     * 商户入金总额 - 交易订单买入
     *
     * @param $userIds
     * @param int $currency
     * @return mixed
     */
    public function merchantOrder($userIds, $currency = Currency::USDT)
    {
        $orders = OtcOrder::type(OtcOrder::BUY)
            ->currency($currency)
            ->status(OtcOrder::RECEIVED)
            ->whereIn('user_id', $userIds)
            ->get();

        return $orders;
    }

    /**
     * 商户出金总额 - 快捷抢单
     *
     * @param $userIds
     * @return mixed
     */
    public function merchantOrderQuick($userIds)
    {
        $ordersQuick = OtcOrderQuick::status(OtcOrderQuick::RECEIVED)
            ->whereIn('owner_id', $userIds)
            ->get();

        return $ordersQuick;
    }


    /**
     * 财务报表数据概览
     *
     * @return array
     */
    public static function exportReport()
    {
        bcscale(config('app.bcmath_scale'));

        // 注册用户数
        $users = self::getUser();

        // 最近7天新增
        $lastSevenDayUser = self::getLastSevenDayUser(7);

        // OTC 累计充值数额(USDT)
        $otcDepositAmount = self::getOtcTransactions(WalletTransaction::DEPOSIT);

        // OTC 累计提币数额(USDT)
        $otcWithdrawAmount = self::getOtcTransactions(WalletTransaction::WITHDRAW);

        // OTC 累计买入交易数量及手续费(USDT)
        $otcBuyTotal =  self::otcOrderTotal();

        // OTC 累计卖出交易数量及手续费(USDT)
        $otcSellTotal =  self::otcOrderTotal(OtcOrder::SELL);

        // OTC 累计交易手续费 (USDT)
        $otcFee = bcadd($otcBuyTotal->fee, $otcSellTotal->fee);

        // OTC 累计充提币手续费(USDT)
        $transFeeDeposit = self::walletTransFee(WalletTransaction::DEPOSIT);
        $transFeeWithdraw = self::walletTransFee(WalletTransaction::WITHDRAW);
        $walletFee = bcadd($transFeeDeposit, $transFeeWithdraw);

        // OTC 快捷购买溢价收益(USDT)
        $otcQuickIncomeSys = self::otcQuickIncomeSys();

        // OTC 平台累计收益(USDT)  (RMB)
        $otcSysIncomeTotal = bcadd($otcFee, $walletFee);
        $otcSysIncomeTotal = bcadd($otcSysIncomeTotal, $otcQuickIncomeSys);
        $otcSysIncomeTotalRmb = bcmul($otcSysIncomeTotal, LegalCurrency::rmbRate() ?: 0);

        // OTC 累计支出（USDT）
        $otcSysWithdraw =  self::otcSysIncomeWithdraw();
        $otcSysWithdrawRmb = bcmul($otcSysWithdraw, LegalCurrency::rmbRate() ?: 0);

        // OTC 收益余额(USDT) (RMB)
        $otcSysIncomeCurrent = bcsub($otcSysIncomeTotal, $otcSysWithdraw);
        $otcSysIncomeCurrentRmb = bcmul($otcSysIncomeCurrent, LegalCurrency::rmbRate() ?: 0);

        return compact('users','lastSevenDayUser','otcDepositAmount','otcWithdrawAmount',
            'otcBuyTotal','otcSellTotal','otcFee','walletFee','otcQuickIncomeSys',
            'otcSysIncomeTotal','otcSysIncomeTotalRmb',
            'otcSysWithdraw', 'otcSysWithdrawRmb',
            'otcSysIncomeCurrent','otcSysIncomeCurrentRmb');

    }

}
