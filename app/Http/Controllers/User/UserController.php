<?php

namespace App\Http\Controllers\User;

use App\Models\Country;
use App\Models\Currency;
use App\Models\KycLevel;
use App\Models\LegalCurrency;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\OTC\Trade;
use App\Models\Wallet\Balance;
use App\Models\Wallet\WalletTransaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const  USER_LIST_SIZE = 20;

/**
 * Class UserController
 * @package App\Http\Controllers\User
 * 交易用户管理
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //用户状态
        $userStatus = $this->getUserStatus();

        // 认证等级
        $kycLevels = KycLevel::all();

        // 账户类型
        $accountType = User::ACCOUNT_TYPE;

        //用户UID或用户名-电话检索
        $search = trim($request->search,'');
        $searchId = trim($request->searchId,'');
        $filterType= trim($request->filterType,'');
        $filterVerify= trim($request->filterVerify,'');
        $orderC = trim($request->orderC,'');

        $user = User::when($searchId, function ($query) use ($searchId){
                return $query->where('id', $searchId);
            })
            ->when($search, function ($query) use ($search){
                $query->where(function ($query) use ($search) {
                    $query->where('username','like',"%$search%")
                        ->orwhere('email','like',"%$search%")
                        ->orwhere('phone', 'like', "%$search%");
                });
            })
            ->when($filterType, function ($query) use ($filterType){
                return $query->where('account_type', $filterType);
            })
            ->when($filterVerify, function ($query) use ($filterVerify){
                return $query->where('verify_status', $filterVerify);
            })
            ->when($orderC, function ($query) use ($orderC) {
                return $query->orderBy('created_at', $orderC);
            }, function ($query) use ($orderC) {
                return $query->orderBy('created_at', 'desc');
            })
            ->paginate(USER_LIST_SIZE );

        return view('user.userIndex', compact('userStatus', 'kycLevels', 'accountType','search','searchId', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        $uri = \Request::get('uri') ?? 'user/mange';

        // 国家信息
        $country = Country::all()->pluck('name','id')->toArray();

        // 认证等级
        $kycLevels = KycLevel::all();

        // 认证状态
        $kycStatus = $this->getUserStatus()['verify_status'];

        // 用户交易数据
        $transaction = $this->transaction($user->id);

        return view('user.userKycShow', compact('user','uri','country','kycLevels','kycStatus','transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // TODO 用户信息-认证-领导人级别-邀请码-代码重构
        $query = DB::table('users')->where('id', $id);
        $find = User::findOrFail($id);
        $user = [
            $request->field => $request->update,
            'updated_at' => self::carbonNow(),
        ];

        // 更新认证等级和认证状态
        if ($request->field == 'kyc_level_id') {
            $verify = ['verify_status' => User::VERIFIED];
            $user = $user + $verify;

            // 如认证通过（高级认证）则分配邀请码
            if ($request->update == KycLevel::ADVANCED) {
                $leader = ['invite_code'  => strtolower(str_random(6))];
                $user = $user + $leader;
            }
        }

        if ($query->update($user)) {
            if ($request->field == 'kyc_level_id') {
                return back();
            }
            return response()->json(['code' =>0, 'msg' => '更新成功' ]);
        }
    }

    /**
     * 重置用户密码及认证
     *
     * @param Request $request
     * @param $uid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accountReset(Request $request, $uid)
    {
        $user = User::findOrFail($uid);

        if ($request->has('paypwd')) {
            $user->pay_password = bcrypt($request->paypwd ? trim($request->paypwd):config('conf.def_user_pay_pwd'));
        }

        if ($request->has('pwd')) {
            $user->password = bcrypt($request->pwd ? trim($request->pwd):config('conf.def_user_pwd'));
        }

        $user->save();

        return back()->with('msg', '操作成功');
    }

    /**
     * 用户账户冻结
     *
     * @param $uid
     * @return array|mixed
     * @throws \Throwable
     */
    public function accountFrozen($uid)
    {
        $user = User::findOrFail($uid);

        // 取消冻结
        if (!$user->is_valid) {

            $user->is_valid = User::ACTIVE;
            $user->save();
            return  ['code' => 0, 'msg' => '已解冻'];
        }

        // 冻结账户
        $trades = $user->trades()
            ->whereIn('status', [Trade::ON_SALE])
            ->pluck('id');

        $frozen = DB::transaction(function () use ($user, $trades){

            $success =  ['code' => 0, 'msg' => '已冻结'];

            // 广告不存在
            if ($trades->isEmpty()) {
                $user->is_valid = User::FORBIDDEN;
                $user->save();
                return $success;
            }

            foreach ($trades as $key => $tradeId) {

                $trade = $user->trades()
                    ->whereIn('status', [Trade::ON_SALE])
                    ->find($tradeId);

                // 判断是否有正在进行中的订单
                if ($trade->pendingOrders()->count()) {
                    continue;
                }

                // 广告下架
                $trade->status = Trade::OFF;
                $trade->save();

                $user->is_valid = User::FORBIDDEN;
                $user->save();
            }

            return $success;
        });

        return $frozen;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (DB::table('users')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }

    /**
     * 用户认证待审核列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pendingUser(Request $request)
    {
        //用户状态
        $userStatus = $this->getUserStatus();

        //用户名-电话检索
        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'');

        // 认证等级
        $kycLevels = KycLevel::all();

        // 账户类型
        $accountType = User::ACCOUNT_TYPE;

        $user = DB::table('users as u')
            ->where('verify_status',2)
            ->when($search, function ($query) use ($search){
                return $query->where('username','like',"%$search%")
                    ->orwhere('email','like',"%$search%")
                    ->orwhere('phone', 'like', "%$search%");
            })
            ->when($orderC, function ($query) use ($orderC) {
                return $query->orderBy('created_at', $orderC);
            }, function ($query) use ($orderC) {
                return $query->orderBy('created_at', 'desc');
            })
            ->paginate(USER_LIST_SIZE );

        return view('user.userIndex', compact('userStatus', 'search', 'kycLevels','accountType','user'));
    }

    /**
     * 用户账户及认证状态信息
     *
     * @return array
     */
    public function getUserStatus()
    {
        return [
            'email_phone_status' => [
                1 => ['name' => '未验证' ,'class' => 'default'],
                2 => ['name' => '已验证' ,'class' => 'success'],
            ],
            'google_status' => [
                1 => ['name' => '未绑定' ,'class' => 'default'],
                2 => ['name' => '绑定未开启' ,'class' => 'info'],
                3 => ['name' => '已开启' ,'class' => 'success'],
            ],
            'is_valid' => [
                0 => ['name' => '禁用' ,'class' => 'danger'],
                1 => ['name' => '正常' ,'class' => 'success'],
            ],
            'verify_status' => [
                1 => ['name' => '未认证' ,'class' => 'default'],
                2 => ['name' => '待审核' ,'class' => 'info'],
                3 => ['name' => '已认证' ,'class' => 'success'],
                4 => ['name' => '认证失败' ,'class' => 'warning'],
            ],
            'gender' => [
                0 => ['name' => '保密' ],
                1 => ['name' => '男'],
                2 => ['name' => '女'],
            ]
        ];

    }

    /**
     * 用户交易数据统计
     *
     * @param $uid
     * @return array
     */
    public static function transaction($uid)
    {
        bcscale(config('app.bcmath_scale'));

        // 累计充值数额
        $deposit = WalletTransaction::where('user_id', $uid)
            ->select(DB::raw('sum(amount) as amount'),DB::raw('sum(fee) as fee'))
            ->type(WalletTransaction::DEPOSIT)
            ->currency(Currency::USDT)
            ->status(WalletTransaction::SUCCESS)
            ->first();

        // 累计提币数额
        $withdraw = WalletTransaction::where('user_id', $uid)
            ->select(DB::raw('sum(amount) as amount'),DB::raw('sum(fee) as fee'))
            ->type(WalletTransaction::WITHDRAW)
            ->currency(Currency::USDT)
            ->status(WalletTransaction::SUCCESS)
            ->first();

        // 累计入金交易量 - （即用户买入-广告商卖出）
        $sell = OtcOrder::where('from_user_id', $uid)
            ->select(DB::raw('sum(field_amount) as amount'),DB::raw('sum(fee) as fee'))
            ->type(OtcOrder::BUY)
            ->currency(Currency::USDT)
            ->status(OtcOrder::RECEIVED)
            ->first();

        // 累计出金交易量
        $out = OtcOrderQuick::where('user_id', $uid)
            ->select(DB::raw('sum(field_amount) as amount'),DB::raw('sum(income_sys) as income'))
            ->status(OtcOrderQuick::RECEIVED)
            ->first();

        // 累计充值手续费
        $depositFee = @$deposit->fee;

        // 累计入金交易手续费
        $sellFee = @$sell->fee;

        // 累计出金溢价收益-贡献给平台(快捷抢单-平台收益)
        $outIncome = @$out->income;

        // 合计 - 产生收益
        $contribution  = bcadd(bcadd($depositFee,$sellFee),$outIncome);
        $contributionRmb = bcmul($contribution, LegalCurrency::rmbRate());

        // 累计团队红利
        $balance = Balance::where('user_id', $uid)->currency(Currency::USDT)->first();
        $bonusTotal = @$balance->bonus_total;

        return compact('deposit','withdraw','sell','out','depositFee','sellFee','outIncome',
            'contribution','contributionRmb','bonusTotal');
    }

}
