<?php

namespace App\Traits;


use Illuminate\Support\Facades\Auth;

/**
 * Class MongoLogHelper
 * @package App\Traits
 * 管理端日志记录
 */
trait MongoLogHelper {


    /**
     * 插入日志记录（单条insert）
     *
     * @param $data
     */
    public function LogInto($data, $type='')
    {
        //uid /email / type / ip / session / referer / agent  / datatime/ app / level /route / method / parameter
        // [message] / [context] / [extra]
        $infoInit = [
            'uid' => Auth::id(),
            'email' => Auth::user()->email,
            'type' => $this->getType($type),
            'ip' => \Request::ip(),
            'session' => \Request::cookie()[strtolower(config('app.name')).'_session'] ?? '',
            'referer' => $this->getHead('referer'),
            'agent' => $this->getHead('user-agent'),
            'datetime' => gmdate('Y-m-d H:i:s',time()),
            'app' => $this->beLongToApp()->name ?? '',
            'route' => \Request::path(),
            'method' => \Request::method(),
            'parameter' => \Request::all() ? json_encode(\Request::all()) : '',
            'message' => $this->beLongToApp()->description ?? $this->beLongToApp(),
        ];

        $info = $infoInit + $data;

        //插入日志
        $logResult = \App\MongoLog::insert($info);

        //记录返回结果到文件日志
        info('logRes',[$logResult]);
    }

    /**
     * 头部信息
     *
     * @param $headKey
     * @return mixed|string
     */
    public function getHead($headKey)
    {
        return isset(\Request::header()[$headKey]) ? head(\Request::header()[$headKey]) : '';
    }

    /**
     * 处理类型
     * @param $type
     * @return string
     */
    public function getType($type)
    {
        return in_array(\Request::path(),['login','demo']) ? 'login' : $type;

    }

    /**
     * 操作描述
     *
     * @return string
     */
    public function beLongToApp()
    {
        $method = \Request::method();
        $path = \Request::path();
        $segment = \Request::segment(3) ?: '';

        $msgL = in_array($path,['login','demo']) ? true : '';
        if ($msgL || ($path == 'home' && str_contains($this->getHead('referer'),'login'))) { return '登录系统';}
        if($path == 'home'){ return '浏览系统数据面板'; }

        if ($segment && is_numeric($segment)) {
            $pathArray = explode(\Request::segment(3),\Request::path());
            $pathArray[0] .='{'.\Request::segment(2).'}';
            $path = implode($pathArray);
        }

        $appInfo = \DB::table('auth_permissions as p')->join('auth_modules as m','p.module_id', 'm.id')
            ->where('p.method',$method)->where('p.uri',$path)
            ->get(['p.description','m.name'])->first();

        return $appInfo;
    }
} 