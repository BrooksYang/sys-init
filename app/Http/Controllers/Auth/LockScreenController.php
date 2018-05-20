<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LockScreenController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 加载锁屏页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lockScreen()
    {
        session(['locked' => 'true']);

        return view('lock-screen');
    }

    /**
     * 屏幕解锁
     *
     * @param Request $request
     * @return
     */
    public function unlock(Request $request)
    {

        $password = $request->password;

        $this->validate($request, [
            'password' => 'required',
        ],['password.required' => '* 密码不能为空']);

        if(\Hash::check($password, \Auth::user()->password)){
            info('hasCheck',[$request->session()->get('locked')]);
            $request->session()->forget('locked');
            info('hasCheck2',[$request->session()->get('locked')]);

            return redirect('/home');
        }

        return back()->withErrors('* 验证失败请重试');
    }
}
