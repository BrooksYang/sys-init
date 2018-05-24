<?php

namespace App\Traits;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'parameter' => \Request::getQueryString(),
            'message' => $this->beLongToApp()->description ?? $this->beLongToApp(),
        ];

        $info = $infoInit + $data;

        //插入 mongo 日志
        $logResult = \App\MongoLog::insert($info);

        //记录 mongo 插入返回结果到文件日志
        Log::info($logResult);
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
        $path = \Request::path();
        $typeL = in_array(\Request::path(),['login','demo']) ? true : $type;
        $typeH = ($path=='home' && str_contains($this->getHead('referer'), 'login')) ? true : $type;
        if ($typeL || $typeH) { return 'login';}

        return $type;
    }

    /**
     * 操作描述
     *
     * @return string
     */
    public function beLongToApp()
    {
        $method = \Request::method();
        $uri = \Request::path();

        $msgL = in_array(\Request::path(),['login','demo']) ? true : '';
        $msgH = ($uri=='home' && str_contains($this->getHead('referer'), 'login')) ? true : '';
        if ($msgL || $msgH) { return '登录系统'; }
        if ($uri=='home') { return '浏览系统数据面板'; }

        if (\Request::segment(3)) {
            $uriArray = explode(\Request::segment(3),\Request::path());
            $uriArray[0] .='{'.\Request::segment(2).'}';
            $uri = implode($uriArray);
        }

        $appInfo = \DB::table('auth_permissions as p')->join('auth_modules as m','p.module_id', 'm.id')
            ->where('p.method',$method)->where('p.uri',$uri)
            ->get(['p.description','m.name'])->first();

        return $appInfo;
    }
} 