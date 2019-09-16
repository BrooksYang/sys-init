<?php

namespace App\Http\Controllers\Finance;

use App\Models\Currency;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\Wallet\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends Controller
{

    const EXPORT_PER_SIZE = 100;

    /**
     * 收益报表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function incomeDaily(Request $request)
    {
        // 每天-%Y-%m-%d  每周-%Y-%u  每月-%Y-%m
        $groups = [
            'day'   => ['name'=>'按日'],
            'week'  => ['name'=>'按周'],
            'month' => ['name'=>'按月']
        ];

        // 多条件搜索
        $searchGroup = trim($request->searchGroup ?: 'day','');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $dateFormat = '%Y-%m-%d';

        if ($searchGroup == 'week') {
            $dateFormat = '%Y-%u';
        }

        if ($searchGroup == 'month') {
            $dateFormat = '%Y-%m';
        }

        $search = $searchGroup || $start || $end;

        // 获取OTC平台各项收益
        $otcSysIncome = $this->getOtcSysIncome($dateFormat, $start, $end);

        $statistics = $this->sum($otcSysIncome);
        $otcSysIncome = self::selfPage($otcSysIncome, config('app.pageSize'));

        return view('finance.incomeIndex', compact('otcSysIncome', 'groups', 'search','statistics'));
    }

    /**
     * 获取平台收益
     *
     * @param $dateFormat
     * @param $start
     * @param $end
     * @return array
     */
    public function getOtcSysIncome($dateFormat, $start, $end)
    {
        // OTC 订单买入及手续费统计-每天 - 默认USDT
        $otcBuyOfDay = $this->otcOrderOfDay(OtcOrder::BUY, $dateFormat, $start, $end);

        // 钱包交易手续费-充值-每天 - 默认USDT
        $transFeeDepositOfDay = $this->walletTransFeeOfDay(WalletTransaction::DEPOSIT, $dateFormat, $start, $end);

        // OTC 快捷抢单-平台累计收益-每天 - USDT
        $otcQuickIncomeSysOfDay = $this->otcQuickIncomeSysOfDay($dateFormat, $start, $end);

        // OTC 平台收益统计-每天 默认USDT
        $otcSysIncome = $this->sysFeeIncome($otcBuyOfDay, $transFeeDepositOfDay, $otcQuickIncomeSysOfDay);

        krsort($otcSysIncome);
        $key = 0;
        foreach ($otcSysIncome as $time=>$income) {
            $otcSysIncome[$time]['key'] = $key++;
        }

        return $otcSysIncome;
    }

    /**
     * 搜索统计
     *
     * @param $otcSysIncome
     * @return array
     */
    public function sum($otcSysIncome)
    {
        //bcscale(config('app.bcmath_scale'));
        list($totalBuyFee, $totalDepositFee, $totalQuickIncome,$totals)= [0, 0, 0, 0];

        foreach ($otcSysIncome ?? [] as $key => $item){
            $totalBuyFee += $item['otc_buy_fee'];
            $totalDepositFee += $item['deposit_fee'];
            $totalQuickIncome += $item['quick_income'];
            $totals += $item['total'];
        }

        return compact('totalBuyFee','totalDepositFee','totalQuickIncome','totals');
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
     * OTC 快捷抢单-平台累计收益 - 每天
     *
     * @param string $dateFormat
     * @param string $start
     * @param string $end
     * @return mixed
     */
    public function otcQuickIncomeSysOfDay($dateFormat = '%Y-%m-%d', $start='', $end='')
    {
        $otcQuickIncomeSysOfDay = OtcOrderQuick::status(OtcOrderQuick::RECEIVED)
            ->select(\DB::raw("DATE_FORMAT(created_at, '$dateFormat') as time,sum(income_sys) as income"))
            ->when($start, function ($query) use ($start) {
                $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at','<=', $end);
            })
            ->groupBy('time')
            ->get();

        return $otcQuickIncomeSysOfDay;
    }

    /**
     * OTC订单买入或卖出及手续费统计 - 默认USDT
     *
     * @param $type
     * @param string $dateFormat
     * @param string $start
     * @param string $end
     * @param int $currency
     * @return mixed
     */
    public function otcOrderOfDay($type, $dateFormat = '%Y-%m-%d',$start='', $end='', $currency = Currency::USDT)
    {
        $otcOrderOfDay = OtcOrder::type($type)
            ->currency($currency)
            ->status(OtcOrder::RECEIVED)
            ->select(\DB::raw("DATE_FORMAT(created_at, '$dateFormat') as time,sum(field_amount) as amount,sum(fee) as fee"))
            ->when($start, function ($query) use ($start) {
                $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at','<=', $end);
            })
            ->groupBy('time')
            ->get();

        return $otcOrderOfDay;
    }

    /**
     * 钱包交易手续费-每天 - 默认USDT
     *
     * @param $type
     * @param string $dateFormat
     * @param string $start
     * @param string $end
     * @param int $currency
     * @return mixed
     */
    public function walletTransFeeOfDay($type, $dateFormat = '%Y-%m-%d', $start='', $end='',$currency = Currency::USDT)
    {
        $walletTransFeeOfDay = WalletTransaction::type($type)
            ->currency($currency)
            ->status(WalletTransaction::SUCCESS)
            ->select(\DB::raw("DATE_FORMAT(created_at, '$dateFormat') as time,sum(amount) as amount,sum(fee) as fee"))
            ->when($start, function ($query) use ($start) {
                $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('created_at','<=', $end);
            })
            ->groupBy('time')
            ->get();

        return $walletTransFeeOfDay;
    }

    /**
     * OTC 平台收益统计 - 每天 默认USDT
     *
     * @param $otcBuyOfDay
     * @param $depositFeeOfDay
     * @param $otcQuickIncomeSysOfDay
     * @return array
     */
    public function sysFeeIncome($otcBuyOfDay, $depositFeeOfDay, $otcQuickIncomeSysOfDay)
    {
        bcscale(config('app.bcmath_scale'));
        $sysIncome = [];

        $otcBuy = $otcBuyOfDay->pluck('fee','time');
        $deposit = $depositFeeOfDay->pluck('fee','time');
        $quickIncome = $otcQuickIncomeSysOfDay->pluck('income','time');

        $otcBuyTime = $otcBuy->keys();
        $depositTime = $deposit->keys();
        $times = $otcBuyTime->merge($depositTime)->sort();

        foreach ($times as $time) {
            $sysIncome[$time]['otc_buy_fee'] = $otcBuy[$time] ?? 0;
            $sysIncome[$time]['deposit_fee'] = $deposit[$time] ?? 0;
            $sysIncome[$time]['quick_income'] = $quickIncome[$time] ?? 0;
            $sysIncome[$time]['total'] = bcadd(bcadd($otcBuy[$time] ?? 0, $deposit[$time] ?? 0), $quickIncome[$time] ?? 0);
        }


        return $sysIncome;
    }

    /**
     * 收益记录-导出Excel
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
        $columns = [ '日期','交易手续费','充值手续费','出金溢价收益','小计'];

        $timeFlag = ($start ?:'开始').'-'.($end ?:'当前');
        if (!($start || $end)) { $timeFlag = Carbon::now()->toDateString(); }
        $fileName = 'OTC收益报表_'.$timeFlag;

        // 处理数据
        $rowData = []; $dataFlag = true;
        $list = $this->getOtcSysIncome($dateFormat,$start, $end);

        foreach($list as $key=>$item) {
            $rowData[$key]['time'] = ($key.$dateUnit ?? '') ?? '';
            $rowData[$key]['otc_buy_fee'] = $item['otc_buy_fee'] ?? '';
            $rowData[$key]['deposit_fee'] = $item['deposit_fee'] ?? '';
            $rowData[$key]['quick_income'] = $item['quick_income'] ?? '';
            $rowData[$key]['total'] = $item['total'] ?? '';
        }

        // 数据总计
        $rowData[$item['key']+1][] = '总计';
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totalBuyFee'];
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totalDepositFee'];
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totalQuickIncome'];
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totals'];

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

            $excel->sheet($dataFlag ? '收益报表': '无数据', function ($sheet) use ($rowData, $newRowData, $columns, $dataFlag) {
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
            'B'     =>  25,
            'C'     =>  25,
            'D'     =>  25,
            'E'     =>  25,
        ));

        return $sheet;
    }

}
