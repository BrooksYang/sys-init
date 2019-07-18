<?php

namespace App\Http\Controllers\User;


use App\Http\Requests\UserAppKeyRequest;
use App\Models\Country;
use App\Models\OTC\UserAppKey;
use App\User;
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
}
