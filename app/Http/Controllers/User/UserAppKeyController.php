<?php

namespace App\Http\Controllers\User;


use App\Http\Requests\UserAppKeyRequest;
use App\Models\Country;
use App\Models\LegalCurrency;
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

        // 账号类型
        $type = UserAppKey::TYPE;

        // 通道状态
        $isOpen = UserAppKey::IS_OPEN;

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

        return view('user.userAppKeyIndex', compact('search','status','type','isOpen','users'));
    }

    /**
     * 商户创建
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $countries = $this->countries;
        $types = UserAppKey::TYPE;

        return view('user.merchantCreate', compact('countries','types'));
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
                'type'        => $request->type,
                'ip'          => $ip,
                'expired_at'  => $expiredAt,
                'is_enabled'  => $request->is_enabled,
                'start_time'  => $request->start_time,
                'end_time'    => $request->end_time,
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
            'types' => UserAppKey::TYPE,
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
            $ip = $request->ip ? json_encode(explode(',', $request->ip)) : [];
            $expiredAt = $ip ? null : ($request->expired_at ?: Carbon::parse('+90 days')->toDateTimeString());

            // 更新appKey相关信息
            //$userAppKey->type = $request->type;
            $userAppKey->ip = $ip ?: '[]';
            $userAppKey->expired_at = $expiredAt;
            $userAppKey->remark = $request->remark;
            $userAppKey->is_enabled = $request->is_enabled;
            $userAppKey->start_time = $request->start_time;
            $userAppKey->end_time = $request->end_time;
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
        $data = self::merchantExchange($id);

        return view('user.userWalletShow', $data);
    }

    /**
     * 商户交易数据
     *
     * @param $id
     * @return array
     */
    public static function merchantExchange($id)
    {
        bcscale(config('app.bcmath_scale'));

        // 用户起始id - 133; 自有币商id - 277
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
        $currentBalance = @$balance->user_wallet_balance + @$balance->user_wallet_balance_freeze_amount; // 当前余额
        $currentBalanceRmb = bcmul($currentBalance, LegalCurrency::rmbRate()); // 当前余额-RMB
        $available = @$balance->user_wallet_balance ; // 可用余额
        $frozen = @$balance->user_wallet_balance_freeze_amount; // 冻结余额


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
            ->whereNotIn('user_id', [$id, 277])
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

        return compact('merchant', 'totalTradesSell','field','final','withdraw',
            'sell','out','correctBalance','currentBalance', 'currentBalanceRmb','available','frozen',
            'totalDeposit','totalBalance','totalLeft');
    }

    /**
     * 商户贡献收益 - 入金/出金
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function incomeShow($id, Request $request)
    {
        $start = trim($request->start);
        $end = trim($request->end);

        $merchant = User::find($id);

        // 商户旗下用户id
        $userIds = $merchant->appKey->users()->pluck('id')->toArray();

        // 入金贡献收益
        $in = OtcOrder::whereIn('type', [OtcOrder::BUY, OtcOrder::SELL])
            ->whereIn('user_id', $userIds)
            ->currency(Currency::USDT)
            ->status(OtcOrder::RECEIVED)
            ->when($start, function ($query) use ($start) {
                $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('updated_at', '<=', $end);
            })
            ->sum('fee');

        // 出金贡献收益
        $out = OtcOrderQuick::whereIn('owner_id', $userIds)
            ->status(OtcOrderQuick::RECEIVED)
            ->when($start, function ($query) use ($start) {
                $query->where('updated_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('updated_at', '<=', $end);
            })
            ->sum('income_sys');

        return compact('in','out');
    }

    /**
     * 设置或取消用户账户类型为商户
     *
     * @param $id
     * @return array
     * @throws \Throwable
     */
    public function setMerchant($id)
    {
        $user = User::findOrFail($id);

        DB::transaction(function () use ($user) {
            // 账户类型
            $user->is_merchant = $user->is_merchant == User::NOT_MERCHANT ? User::MERCHANT : User::NOT_MERCHANT;
            $user->save();

            // 生成key - 默认普通商户
            if ($user->is_merchant == User::MERCHANT) {
                $user->appKey()->create([
                    'access_key'  => Str::uuid(),
                    'secret_key'  => Str::uuid(),
                    //'type'      => $request->type
                ]);
            }

            // 删除key
            if ($user->is_merchant == User::NOT_MERCHANT) {
                $user->appKey->delete();
            }
        });

        return response()->json(['code'=>200, 'msg'=>'更新成功']);
    }

}
