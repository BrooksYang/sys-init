<?php

namespace App\Http\Controllers\User;


use App\Models\OTC\UserAppKey;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 商户密钥管理
 *
 * Class UserAppKeyController
 * @package App\Http\Controllers\User
 */
class UserAppKeyController extends Controller
{
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

}
