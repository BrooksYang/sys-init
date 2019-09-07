<?php

namespace App\Http\Controllers\User;


use App\Http\Requests\UserAppKeyRequest;
use App\Models\Country;
use App\Models\OTC\UserAppKey;
use App\User;
use App\Models\Currency;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\OTC\Trade;
use App\Models\Wallet\Balance;
use App\Models\Wallet\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 商户密钥管理
 *
 * Class UserAppKeyController
 * @package App\Http\Controllers\User
 */
class UserAppKeyController extends Controller
{

    protected $countries;

    public function __construct()
    {
       $this->countries = Country::oldest()->get();
    }

    /**
     * 商户秘钥列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 用户名-电话-身份证号 或 密钥、签名检索
        $search = trim($request->search,'');
        $orderC = trim($request->orderC ?:'desc','');

        // 账号状态
        $status = User::STATUS;

        $users =  UserAppKey::with('user')
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('user', function ($query) use($search){
                    return $query->where('phone','like', "%$search%")
                        ->orWhere('username','like', "%$search%")
                        ->orWhere('email','like', "%$search%")
                        ->orWhere('id_number','like', "%$search%");
                });
            })
            ->when($search, function ($query) use ($search){
                return $query->orWhere('access_key','like', "%$search%");
            })
            ->when($search, function ($query) use ($search){
                return $query->orWhere('secret_key','like', "%$search%");
            })
            ->when($orderC, function ($query) use ($orderC) {
                return $query->orderBy('created_at', $orderC);
            })
            ->paginate(config('app.pageSize'));

        return view('user.userAppKeyIndex', compact('search','status','users'));
    }

    /**
     * 商户创建
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $countries = $this->countries;

        return view('user.merchantCreate', compact('countries'));
    }

    /**
     * 保存商户
     *
     * @param UserAppKeyRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function store(UserAppKeyRequest $request)
    {
        DB::transaction(function () use ($request){

            // 生成用户
            $uid = User::insertGetId([
                'is_merchant' => User::MERCHANT,
                'country_id'  => $request->country_id,
                'username'    => $request->username ?: '',
                'phone'       => $request->phone ?: null,
                'email'       => $request->email ?: null,
                'id_number'   => $request->id_number ?: null,

                'nationality' => $request->nationality ?: 'cn',
                'password'    => bcrypt(config('conf.merchant_pwd')),
            ]);

            // 绑定ip后系统生成的key永久有效，未绑定ip过期时间为90天
            $ip = $request->ip ? json_encode(explode(',', $request->ip)) : null;
            $expiredAt = $ip ? null : Carbon::parse('+90 days')->toDateTimeString();

            // 生成key
            UserAppKey::create([
                'user_id'     => $uid,
                'access_key'  => Str::uuid(),
                'secret_key'  => Str::uuid(),
                'ip'          => $ip,
                'expired_at'  => $expiredAt,
                'remark'      => $request->remark
            ]);

        });

        return redirect('user/merchant');
    }

    /**
     * 编辑商户
     * 
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $userAppKey = UserAppKey::findOrFail($id);
        $user = UserAppKey::with('user')->where('user_id', $userAppKey->user_id)->firstOrFail();

        return view('user.merchantCreate',[
            'editFlag' => true,
            'countries' => $this->countries,
            'user' => $user
        ]);
    }

    /**
     * 更新用户及相关appKey信息
     * 
     * @param UserAppKeyRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UserAppKeyRequest $request, $id)
    {
        $userAppKey = UserAppKey::findOrFail($id);
        $user = User::findOrFail($userAppKey->user_id);

        DB::transaction(function () use ($user, $userAppKey, $request){

            // 更新用户信息
            $user->country_id = $request->country_id;
            $user->username = $request->username ?: '';
            $user->phone = $request->phone ?: null;
            $user->email = $request->email ?: null;
            $user->id_number = $request->id_number ?: null;
            $user->nationality = $request->nationality ?: 'cn';
            $user->save();

            // 绑定ip后系统生成的key永久有效，未绑定ip过期时间为90天
            $ip = $request->ip ? json_encode(explode(',', $request->ip)) : null;
            $expiredAt = $ip ? null : Carbon::parse('+90 days')->toDateTimeString();

            // 更新appKey相关信息
            $userAppKey->ip = $ip;
            $userAppKey->expired_at = $expiredAt;
            $userAppKey->remark = $request->remark;
            $userAppKey->save();
        });
        
        return redirect('user/merchant');
    }

    /**
     * 修改用户账户状态
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeAccountStatus(Request $request,$id)
    {
        $userAppKey = UserAppKey::findOrFail($id);
        $user = User::findOrFail($userAppKey->user_id);

        $user->is_valid = $user->is_valid == User::ACTIVE ? User::FORBIDDEN : User::ACTIVE;
        $user->save();

        return response()->json(['code' =>0, 'msg' => '更新成功']);
    }

    /**
     * 商户钱包账户资料信息
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        // 商户
        $merchant = User::find($id);

        // 商户旗下用户id
        $userIds = $merchant->appKey->users()->pluck('id')->toArray();

        // 订单总额
        $orders = OtcOrder::where('type', OtcOrder::BUY)
            ->where('status', OtcOrder::RECEIVED)
            ->whereIn('user_id', $userIds)
            ->get();

        // 广告支出
        $sell = OtcOrder::where('type', OtcOrder::SELL)
            ->where('status', OtcOrder::RECEIVED)
            ->whereIn('user_id', $userIds)
            ->sum('field_amount');

        $field = $orders->sum('field_amount');
        $final = $orders->sum('final_amount');

        // 出金
        $out = OtcOrderQuick::where('merchant_id', $merchant->id)
            ->where('status', OtcOrder::RECEIVED)
            ->sum('merchant_final_amount');

        // 商户余额
        $balance = Balance::where('user_id', $merchant->id)
            ->where('user_wallet_currency_id', Currency::USDT)
            ->first();

        // 商户提币
        $withdraw = WalletTransaction::where('user_id', $merchant->id)
            ->where('type', WalletTransaction::WITHDRAW)
            ->where('status', WalletTransaction::SUCCESS)
            ->sum('amount');

        // 正常余额 = 到账金额 - 提币金额 - 卖出 - 出金
        $correctBalance = $final - $withdraw - $sell - $out;

        // 余额
        $currentBalance = $balance->user_wallet_balance + $balance->user_wallet_balance_freeze_amount; // 当前余额
        $frozen = $balance->user_wallet_balance_freeze_amount; // 冻结余额


        // 用户累计充值
        $deposit = WalletTransaction::where('type', WalletTransaction::DEPOSIT)
            ->where('user_id', '>=', 133)
            ->where('user_id', '!=', 277)
            ->where('status', WalletTransaction::SUCCESS)
            ->get(['id', 'user_id', 'amount', 'fee']);
        $totalDeposit = $deposit->sum('amount') - $deposit->sum('fee');

        // 用户总余额
        $balances = Balance::where('user_wallet_currency_id', Currency::USDT)
            ->where('user_id', '>=', 133)
            ->whereNotIn('user_id', [134, 277])
            ->get();
        $totalBalance = $balances->sum('user_wallet_balance') + $balances->sum('user_wallet_balance_freeze_amount');


        // 累计广告卖出
        $totalTradesSell = Trade::where('type', Trade::SELL)
            ->sum('field_amount');

        // 广告累计余量
        $trades = Trade::where('type', Trade::SELL)
            ->whereIn('status', [Trade::ON_SALE, Trade::OFF])
            ->get();
        $totalLeft = $trades->sum('amount') - $trades->sum('field_amount');

        return view('user.userWalletShow', compact('merchant', 'totalTradesSell','field','final','withdraw',
            'sell','out','correctBalance','currentBalance','frozen', 'totalDeposit','totalBalance','totalLeft'));
    }

}
