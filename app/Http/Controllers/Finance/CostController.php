<?php

namespace App\Http\Controllers\Finance;

use App\Models\Currency;
use App\Models\Wallet\WalletExternal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;

class CostController extends Controller
{
    const EXPORT_PER_SIZE = 100;

    /**
     * 支出报表(收益提取)
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 按日-按周-按月
        $groups = WalletExternal::GROUP;

        // 多条件搜索
        $searchGroup = trim($request->searchGroup ?: 'day','');
        $filterType= trim($request->filterType ?: WalletExternal::WITHDRAW_ADDR,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');

        $type = WalletExternal::TYPE;

        $currencies = Currency::getCurrencies();
        $dateFormat = '%Y-%m-%d';

        if ($searchGroup == 'week') {
            $dateFormat = '%Y-%u';
        }

        if ($searchGroup == 'month') {
            $dateFormat = '%Y-%m';
        }

        $search = $searchGroup || $start || $end;

        $external = WalletExternal::when($filterType, function ($query) use ($filterType) {
                return $query->where('type', $filterType);
            })
            ->select(\DB::raw("DATE_FORMAT(created_at, '$dateFormat') as time, sum(amount) as amount"))
            ->when($start, function ($query) use ($start) {
                $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at','<=', $end);
            })
            ->groupBy('time')
            ->get();

        $statistics = $this->sum($external);

        $sorted = $external->sortByDesc('time');
        $external = $sorted->values()->all();

        $external = self::selfPage($external, config('app.pageSize'));

        return view('finance.costIndex', compact('currencies','type','external','groups','search','statistics'));
    }

    /**
     * 自定义分页
     *
     * @param $items
     * @param $perPage
     * @return LengthAwarePaginator
     */
    public static function selfPage($items, $perPage)
    {
        $pageStart = request('page', 1);
        $offSet    = ($pageStart * $perPage) - $perPage;
        $itemsForCurrentPage = array_slice($items, $offSet, $perPage, TRUE);
        //$itemsForCurrentPage = $items->slice($offSet, $perPage);
        return new LengthAwarePaginator( $itemsForCurrentPage, count($items), $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * 搜索统计
     *
     * @param $external
     * @return array
     */
    public function sum($external)
    {
        //bcscale(config('app.bcmath_scale'));
        list($totalAmount)= [0];

        foreach ($external ?? [] as $key => $item){
            $totalAmount += $item->amount ?? $item;
        }

        return compact('totalAmount');
    }

    /**
     * 提币记录-导出Excel
     * @param Request $request
     */
    public function export(Request $request)
    {
        $searchGroup = $request->searchGroup ?: 'day';
        $start = $request->start ?:'';
        $end = $request->end ?:'';

        $dateFormat = '%Y-%m-%d';
        $dateUnit = '';

        if ($searchGroup == 'week') {
            $dateFormat = '%Y-%u';
            $dateUnit = ' 周';
        }

        if ($searchGroup == 'month') {
            $dateFormat = '%Y-%m';
            $dateUnit = ' 月';
        }

        set_time_limit(0);

        // 设置下载excel文件的headers
        $columns = [ '日期','支出数额'];

        $timeFlag = ($start ?:'开始').'-'.($end ?:'当前');
        if (!($start || $end)) { $timeFlag = Carbon::now()->toDateString(); }
        $fileName = 'OTC收益提取报表_'.$timeFlag;

        // 分批次处理-获取数据总数和设置和计算偏移量
        $num = $this->getExportNum($start, $end, $dateFormat);
        $perSize = self::EXPORT_PER_SIZE;//每次查询的条数
        $pages   = ceil($num / $perSize);
        bcscale(config('app.bcmath_scale'));

        // 处理数据
        $rowData = []; $dataFlag = true;
        for($i = 1; $i <= $pages; $i++) {
            $list = $this->getUnitExportData($i, $perSize, $start, $end, $dateFormat);

            foreach($list as $key=>$item) {
                $rowData[$key][] = ($item->time.$dateUnit ?? '') ?? '';
                $rowData[$key][] = $item->amount ?? '';
            }
        }

        // 数据总计
        $rowData[$key+1][] = '总计';
        $rowData[$key+1][] = @$this->sum(array_column($rowData, 1))['totalAmount'];

        unset($list);

        // 空数据处理
        if (!$rowData) {$rowData[][] = $columns; $dataFlag = null;}

        // 格式化excel数据
        Excel::create($fileName, function ($excel) use ($rowData, $columns, $dataFlag) {
            // 多sheet导出
            /* foreach ($rowData as $key => $leaderTeam) {
                 $excel->sheet($key ?: '无数据', function ($sheet) use ($leaderTeam, $columns, $dataFlag) {
                     if ($dataFlag) { array_unshift($leaderTeam, $columns); }
                     $sheet->rows($leaderTeam);
                     $this->sheetStyle($sheet);
                 });
             };*/

            // 单sheet导出
            $newRowData = $rowData;

            $excel->sheet($dataFlag ? '收益提取': '无数据', function ($sheet) use ($rowData, $newRowData, $columns, $dataFlag) {
                if ($dataFlag) { array_unshift($newRowData, $columns);  $sheet->rows($newRowData); }
                else{ $sheet->rows($newRowData); }
                $this->sheetStyle($sheet);
            });

            // 释放变量
            unset($rowData);
            unset($newRowData);

        })->export('xlsx');

        // 刷新输出缓冲到浏览器
        ob_flush();
        flush();
    }

    /**
     * 设定sheet样式
     *
     * @param $sheet
     * @return mixed
     */
    public function sheetStyle($sheet)
    {
        // 行格式-首行-背景色-加粗和锁定
        $sheet->row(1, function($rowData) {
            $rowData->setBackground('#00B0F0');
        });

        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        // 列宽
        $sheet->setWidth(array(
            'A'     =>  15,
            'B'     =>  22,
        ));

        return $sheet;
    }

    /**
     * 获取数据总量
     * @param $start
     * @param $end
     * @param $dateFormat
     * @return int
     */
    public function getExportNum($start='', $end='', $dateFormat = '%Y-%m-%d')
    {
        $exportNum = WalletExternal::type(WalletExternal::WITHDRAW_ADDR)
            ->select(\DB::raw("DATE_FORMAT(created_at, '$dateFormat') as time, sum(amount) as amount"))
            ->when($start, function ($query) use ($start) {
                $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at','<=', $end);
            })
            ->groupBy('time')
            ->get();

        return $exportNum->count();
    }

    /**
     * 分批获取导出数据
     * @param $i
     * @param $perSize
     * @param $start
     * @param $end
     * @param $dateFormat
     * @return mixed
     */
    public static function getUnitExportData($i, $perSize, $start, $end, $dateFormat = '%Y-%m-%d')
    {
        $export = WalletExternal::type(WalletExternal::WITHDRAW_ADDR)
            ->select(\DB::raw("DATE_FORMAT(created_at, '$dateFormat') as time, sum(amount) as amount"))
            ->when($start, function ($query) use ($start) {
                $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at','<=', $end);
            })
            ->groupBy('time')
            ->skip(($i-1)*$perSize)->take($perSize)
            ->get();

        return $export;
    }

}
