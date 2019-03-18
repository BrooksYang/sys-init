<?php

namespace App\Http\Controllers\User;

use App\Models\Country;
use App\Models\KycLevel;
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

        //用户名-电话检索
        $search = trim($request->search,'');
        $filterObj = trim($request->filterObj,'');
        $filter = trim($request->filter,'');
        $orderC = trim($request->orderC,'');

        $user = DB::table('users as u')
            ->when($search, function ($query) use ($search){
                return $query->where('username','like',"%$search%")
                    ->orwhere('email','like',"%$search%")
                    ->orwhere('phone', 'like', "%$search%");
            })
            ->when($filterObj, function ($query) use ($filterObj,$filter){
                return $query->where($filterObj, $filter);
            })
            ->when($orderC, function ($query) use ($orderC) {
                return $query->orderBy('created_at', $orderC);
            }, function ($query) use ($orderC) {
                return $query->orderBy('created_at', 'desc');
            })
            ->paginate(USER_LIST_SIZE );

        return view('user.userIndex', compact('userStatus', 'kycLevels', 'search','user'));
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

        return view('user.userKycShow', compact('user','uri','country','kycLevels','kycStatus'));
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
        $query = DB::table('users')->where('id', $id);
        $user = [
            $request->field => $request->update,
            'updated_at' => self::carbonNow(),
        ];

        // 更新认证等级和认证状态
        if ($request->field == 'kyc_level_id') {
            $verify = ['verify_status' => User::VERIFIED];
            $user = $user + $verify;
        }

        if ($query->update($user)) {
            if ($request->field == 'kyc_level_id') {
                return back();
            }
            return response()->json(['code' =>0, 'msg' => '更新成功' ]);
        }
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

        return view('user.userIndex', compact('userStatus', 'search', 'kycLevels','user'));
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
}
