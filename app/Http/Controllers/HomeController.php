<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

const EXCHANGE_ORDER_TYPE = [1=>'市价买', 2=>'市价卖', 3=>'限价买', 4=>'限价卖'];
const CACHE_LENGTH = 10;

/**
 * Class HomeController
 * @package App\Http\Controllers
 * Dashboard 统计数据展示
 *
 */
class HomeController extends Controller
{
    public function index()
    {
        //注册用户数
        $users = Cache::remember('users', CACHE_LENGTH, function () {
            return $this->getUser();
        });
        //当天委托订单数
        $exchangeOrders = Cache::remember('exchangeOrders', CACHE_LENGTH, function () {
            return $this->getExchangeOrder();
        });
        //当天成交订单数
        $orderLogs = Cache::remember('orderLogs', CACHE_LENGTH , function () {
            return $this->getOrderLog();
        });
        //当天成交总额
        $orderAmount = Cache::remember('orderAmount', CACHE_LENGTH, function () {
            return $this->orderAmount();
        });
        //用户账户状态
        $userAccountStatus = Cache::remember('userAccountStatus', CACHE_LENGTH, function () use ($users) {
            return $this->userAccountStatusStatistic($users);
        });
        //用户邮箱-手机验证状态统计
        $emailPhoneVerifyStatus = Cache::remember('emailPhoneVerifyStatus', CACHE_LENGTH, function () use ($users) {
            return $this->emailPhoneVerifyStatistic($users);
        });
        //谷歌人机验证状态
        $googleAuth =  Cache::remember('googleAuth', CACHE_LENGTH, function () use ($users){
            return $this->googleAuthStatistic($users);
        });
        //用户认证状态整体分布
        $userVerifyStatus = Cache::remember('userVerifyStatus', CACHE_LENGTH, function () use ($users){
            return $this->userVerifyStatus($users);
        });

        //币种信息
        $currency = Cache::remember('currency', CACHE_LENGTH, function () {
            return $this->getCurrency();
        });
        //当天充值订单数量及金额-按处理状态区分
        $depositOrderStatus = Cache::remember('depositOrderStatus', CACHE_LENGTH, function () {
            return $this->getDepositOrder();
        });
        //当天提币订单数量及金额-按处理状态区分
        $withdrawOrderStatus = Cache::remember('withdrawOrderStatus', CACHE_LENGTH, function () {
            return $this->getWithdrawOrder();
        });
        //当天委托订单数量--按处理状态
        $exchangeOrderByStatus = Cache::remember('exchangeOrderByStatus', CACHE_LENGTH, function () {
            return $this->getExchangeByStatus();
        });
        //当天委托订单成交数量及金额--按类型
        $exchangeOrderByType = Cache::remember('exchangeOrderByType', CACHE_LENGTH, function () {
            return $this->getExchangeOrderByType();
        });
        //当天委托订单成交数量及价格 --按类型
        $exchangeOrderLog = Cache::remember('exchangeOrderLog', CACHE_LENGTH, function () {
            return $this->getExchangeOrderLog();
        });

        //提币订单状态
        //$withdrawOrderStatus = $this->withdrawOrderStatus();
        //提币订单类型
        //$orderType = $this->orderType();

        return view('home' ,compact(
            'users',
            'userAccountStatus',
            'emailPhoneVerifyStatus',
            'googleAuth',
            'userVerifyStatus',
            'exchangeOrders',
            'orderLogs',
            'orderAmount',
            'currency',
            'depositOrderStatus',
            'withdrawOrderStatus',
            'exchangeOrderByStatus',
            'exchangeOrderByType',
            'exchangeOrderLog'
        ));
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
    public function getUser($statusField='', $status = '')
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
        return DB::table('exchange_orders')
            ->when($statusField, function ($query) use($statusField, $status){
                return $query->where($statusField, $status);
            })->where('created_at','like',env('APP_GMDATE', gmdate('Y-m-d')).'%')->count();
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
        return DB::table('exchange_order_logs')
            ->when($statusField, function ($query) use($statusField, $status){
                return $query->where($statusField, $status);
            })->where('created_at','like',env('APP_GMDATE',gmdate('Y-m-d')).'%')->count();
    }

    /**
     * 订单成交总额
     *
     * @return int|mixed
     */
    public function orderAmount()
    {
        $orders = DB::table('exchange_orders')
            ->where('created_at','like',env('APP_GMDATE',gmdate('Y-m-d')).'%')->get(['field_cash_amount']);
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
        $currency = DB::table('dcuex_crypto_currency')->get([
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
        $query = DB::table('dcuex_user_deposit_order');
        $depositOrder['order'] = $query->select(DB::raw("count(deposit_order_status) as orderNum"),
            DB::raw("sum(deposit_amount) as deposit_amount"),'deposit_order_status')
            ->where('created_at', 'like',env('APP_GMDATE', gmdate('Y-m-d')).'%')
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
        $query = DB::table('dcuex_user_withdraw_order');
        $withdrawOrder['order'] = $query->select(DB::raw("count(withdraw_order_status) as orderNum"),
            DB::raw("sum(withdraw_amount) as amount"),'withdraw_order_status as status')
            ->where('created_at', 'like',env('APP_GMDATE', gmdate('Y-m-d')).'%')
            ->groupBy('withdraw_order_status')->orderBy('withdraw_order_status','asc')
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
        $orderStatus = DB::table('exchange_orders')->select(DB::raw('count(type) as statusNum'), 'status')
            ->where('created_at' ,'like', env('APP_GMDATE',gmdate('Y-m-d')).'%')
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
       $orderByType['order'] =  DB::table('exchange_orders')
            ->select(DB::raw('sum(field_amount) as amount'), 'type', DB::raw('sum(field_cash_amount) as cash_amount'))
            ->where('created_at' ,'like', env('APP_GMDATE',gmdate('Y-m-d')).'%')
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
        $orderLog['order'] =  DB::table('exchange_order_logs')
            ->select(DB::raw('sum(field_amount) as amount'), 'type', DB::raw('sum(price) as price'))
            ->where('created_at' ,'like', env('APP_GMDATE',gmdate('Y-m-d')).'%')
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
}
