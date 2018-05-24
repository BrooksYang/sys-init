<?php

namespace App\Http\Controllers\Log;

use App\MongoLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

const LOG_PAGE_SIZE = 20;

/**
 * Class BackendLogController
 * @package App\Http\Controllers\Log
 * 管理端日志
 *
 */
class BackendLogController extends Controller
{
    /**
     * 日志级别及请求方法定义
     *
     * @param $type
     * @return mixed
     */
    public function logConfig($type)
    {
        //Level
        $logConfig = [
            'level' => [
                'Emergency' => ['name' => 'Emergency',   'class' => 'danger'],
                'Critical'  => ['name' => 'Critical',    'class' => 'danger'],
                'Alert'     => ['name' => 'Alert' ,      'class' => 'primary'],
                'Error'     => ['name' => 'Error' ,      'class' => 'warning'],
                'Warning'   => ['name' => 'Warning' ,    'class' => 'warning'],
                'Notice'    => ['name' => 'Notice' ,     'class' => 'success'],
                'Info'      => ['name' => 'Info' ,       'class' => 'default'],
                'Debug'     => ['name' => 'Debug' ,      'class' => 'default']
            ],
            //Method
            'method' => [
                'GET'    => ['name' => 'GET',    'class' => 'default'],
                'POST'   => ['name' => 'POST',   'class' => 'success'],
                'DELETE' => ['name' => 'DELETE', 'class' => 'warning'],
                'PATCH'  => ['name' => 'PATCH',  'class' => 'primary'],
                'PUT'    => ['name' => 'PUT',    'class' => 'primary']
            ]
        ];


        return $logConfig[$type];
    }

    /**
     * 日志信息
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'');
        $filterLevel= trim($request->filterLevel,'');
        $filterMethod= trim($request->filterMethod,'');
        $filterStartAt= trim($request->filterStartAt,'');
        $filterEndAt= trim($request->filterEndAt,'');

        $method = $this->logConfig('method');
        $level = $this->logConfig('level');

        $log = MongoLog::when($filterLevel, function ($query) use ($filterLevel){
                return $query->where('level', $filterLevel);
            })
            ->when($filterMethod, function ($query) use ($filterMethod){
                return $query->where('method', $filterMethod);
            })
            ->when($search, function ($query) use ($search){
                return $query->where('app','like',"%$search%")
                    ->orwhere('email', 'like', "%$search%")
                    ->orwhere('ip', 'like', "%$search%");
            })
            ->when($filterStartAt, function ($query) use ($filterStartAt) {
                return $query->where('datetime', '>=', $filterStartAt);
            })
            ->when($filterEndAt, function ($query) use ($filterEndAt) {
                return $query->where('datetime', '<=', $filterEndAt);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->where('datetime','>',0)->orderBy('', $orderC);
            }, function ($query) {
                return $query->orderBy('datetime', 'desc'); //默认降序
            })
            ->paginate(LOG_PAGE_SIZE);

        return view('log.logIndex', compact('level','method', 'log'));
    }
}
