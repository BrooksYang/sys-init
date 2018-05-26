<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\MongoLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 重写Logout方法
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        MongoLog::where('session',\Request::cookie()[str_slug(env('APP_NAME', 'laravel'), '_').'_session'])
            ->where('type','login')
            ->update(['context' => 'logout#'.gmdate('Y-m-d H:i:s')]);

        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}
