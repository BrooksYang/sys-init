<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\HomeController;
use App\Models\Currency;
use App\Models\LegalCurrency;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcOrderQuick;
use App\Models\Wallet\WalletTransaction;
use App\User;
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
        // 按日-按周-按月
        $groups = OtcOrder::GROUP;
        $merchants = $this->getMerchant();

        // 多条件搜索
        $searchMerchant = trim($request->searchMerchant,'');
        $searchGroup = trim($request->searchGroup ?: 'day','');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $dateFormat = '%Y-%m-%d';
        $uIds = [];

        if ($searchMerchant) {
            // 商户
            $merchant = User::find($searchMerchant);

            // 商户旗下用户id
            $uIds = $merchant->appKey->users()->pluck('id')->toArray();
        }

        if ($searchGroup == 'week') {
            $dateFormat = '%Y-%u';
        }

        if ($searchGroup == 'month') {
            $dateFormat = '%Y-%m';
        }

        $search = $searchGroup || $start || $end;


        // 获取OTC平台各项收益
        $otcSysIncome = $this->getOtcSysIncome(@$merchant->id, $uIds, $dateFormat, $start, $end);

        $statistics = $this->sum($otcSysIncome);
        $otcSysIncome = self::selfPage($otcSysIncome, config('app.pageSize'));

        return view('finance.incomeIndex', compact('otcSysIncome', 'groups', 'merchants','search','statistics'));
    }

    /**
     * 获取系统商户信息
     *
     * @return mixed
     */
    public function getMerchant()
    {
        return User::merchant();
    }

    /**
     * 获取平台收益
     *
     * @param $merchantId
     * @param $uIds
     * @param $dateFormat
     * @param $start
     * @param $end
     * @return array
     */
    public function getOtcSysIncome($merchantId, $uIds, $dateFormat, $start, $end)
    {
        // OTC 订单买入及手续费统计-每天 - 默认USDT
        $otcBuyOfDay = $this->otcOrderOfDay($uIds, OtcOrder::BUY, $dateFormat, $start, $end);

        // 钱包交易手续费-充值-每天 - 默认USDT
        $transFeeDepositOfDay = $this->walletTransFeeOfDay($uIds, WalletTransaction::DEPOSIT, $dateFormat, $start, $end);

        // OTC 快捷抢单-平台累计收益-每天 - USDT
        $otcQuickIncomeSysOfDay = $this->otcQuickIncomeSysOfDay($merchantId, $dateFormat, $start, $end);

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
     * @param $merchantId
     * @param string $dateFormat
     * @param string $start
     * @param string $end
     * @return mixed
     */
    public function otcQuickIncomeSysOfDay($merchantId, $dateFormat = '%Y-%m-%d', $start='', $end='')
    {
        $otcQuickIncomeSysOfDay = OtcOrderQuick::status(OtcOrderQuick::RECEIVED)
            ->select(\DB::raw("DATE_FORMAT(updated_at, '$dateFormat') as time,sum(income_sys) as income"))
            ->when($merchantId, function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId);
            })
            ->when($start, function ($query) use ($start) {
                $query->where('updated_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                $query->where('updated_at','<=', $end);
            })
            ->groupBy('time')
            ->get();

        return $otcQuickIncomeSysOfDay;
    }

    /**
     * OTC订单买入或卖出及手续费统计 - 默认USDT
     *
     * @param $uIds
     * @param $type
     * @param string $dateFormat
     * @param string $start
     * @param string $end
     * @param int $currency
     * @return mixed
     */
    public function otcOrderOfDay($uIds, $type, $dateFormat = '%Y-%m-%d',$start='', $end='', $currency = Currency::USDT)
    {
        $otcOrderOfDay = OtcOrder::type($type)
            ->currency($currency)
            ->status(OtcOrder::RECEIVED)
            ->select(\DB::raw("DATE_FORMAT(updated_at, '$dateFormat') as time,sum(field_amount) as amount,sum(fee) as fee"))
            ->when($uIds, function ($query) use ($uIds) {
                $query->whereIn('user_id', $uIds);
            })
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
     * @param $uIds
     * @param $type
     * @param string $dateFormat
     * @param string $start
     * @param string $end
     * @param int $currency
     * @return mixed
     */
    public function walletTransFeeOfDay($uIds, $type, $dateFormat = '%Y-%m-%d', $start='', $end='', $currency = Currency::USDT)
    {
        $walletTransFeeOfDay = WalletTransaction::type($type)
            ->currency($currency)
            ->status(WalletTransaction::SUCCESS)
            ->select(\DB::raw("DATE_FORMAT(updated_at, '$dateFormat') as time,sum(amount) as amount,sum(fee) as fee"))
            ->when($uIds, function ($query) use ($uIds) {
                $query->whereIn('user_id', $uIds);
            })
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
        $columns = ['日期','交易手续费(USDT)','充值手续费(USDT)','出金溢价收益(USDT)','小计(USDT)', 'RMB'];

        $reportColumns = ['日期', '累计交易手续费(USDT)','累计充提币手续费(USDT)', '出金溢价收益(USDT)',
            '平台累计收益(USDT)','平台累计收益(RMB)', '累计支出(USDT)', '累计支出(RMB)','收益余额(USDT)','收益余额(RMB)',
            '注册用户','最近7天新增','累计充值数额(USDT)','累计提币数额(USDT)', '累计买入交易数量(USDT)',' 累计卖出交易数量(USDT)',];

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
            $rowData[$key]['rmb'] = bcmul($item['total'] ?? 0, LegalCurrency::rmbRate() ?: 0);
        }

        // 数据总计
        $rowData[$item['key']+1][] = '总计';
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totalBuyFee'];
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totalDepositFee'];
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totalQuickIncome'];
        $rowData[$item['key']+1][] = @$this->sum($rowData)['totals'];
        $rowData[$item['key']+1][] = bcmul(@$this->sum($rowData)['totals'], LegalCurrency::rmbRate() ?: 0);

        // 统计数据概览 - sheet2
        $report = HomeController::exportReport();
        $reportData[0][] = '日期';
        $reportData[0][] = Carbon::now()->toDateString();
        $reportData[1][] = '累计交易手续费(USDT)';
        $reportData[1][] = $report['otcFee'];
        $reportData[2][] = '累计充提币手续费(USDT)';
        $reportData[2][] = $report['walletFee'];
        $reportData[3][] = '出金溢价收益(USDT)';
        $reportData[3][] = $report['otcQuickIncomeSys'];
        $reportData[4][] = '平台累计收益(USDT)';
        $reportData[4][] = $report['otcSysIncomeTotal'];
        $reportData[5][] = '平台累计收益(折合RMB)';
        $reportData[5][] = $report['otcSysIncomeTotalRmb'];
        $reportData[6][] = '累计支出(USDT)';
        $reportData[6][] = $report['otcSysWithdraw'];
        $reportData[7][] = '累计支出(折合RMB)';
        $reportData[7][] = $report['otcSysWithdrawRmb'];
        $reportData[8][] = '收益余额(USDT)';
        $reportData[8][] = $report['otcSysIncomeCurrent'];
        $reportData[9][] = '收益余额(折合RMB)';
        $reportData[9][] = $report['otcSysIncomeCurrentRmb'];
        $reportData[10][] = '注册用户数';
        $reportData[10][] = $report['users'];
        $reportData[11][] = '最近7天新增';
        $reportData[11][] = $report['lastSevenDayUser'];
        $reportData[12][] = '累计充值数额(USDT)';
        $reportData[12][] = $report['otcDepositAmount'];
        $reportData[13][] = '累计提币数额(USDT)';
        $reportData[13][] = $report['otcWithdrawAmount'];
        $reportData[14][] = '累计买入交易数量(USDT)';
        $reportData[14][] = @$report['otcBuyTotal']->field_amount;
        $reportData[15][] = '累计卖出交易数量(USDT)';
        $reportData[15][] = @$report['otcSellTotal']->field_amount;

        unset($list);

        // 空数据处理
        if (!$rowData) {$rowData[][] = $columns; $dataFlag = null;}

        // 格式化excel数据
        Excel::create($fileName, function ($excel) use ($rowData, $columns, $dataFlag, $reportData, $reportColumns) {
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

            $excel->sheet('OTC数据概览', function ($sheet) use ($reportData){
                $sheet->rows($reportData);
                $this->reportSheetStyle($sheet);
            });

            $excel->sheet($dataFlag ? '收益报表': '无数据', function ($sheet) use ($rowData, $newRowData, $columns, $dataFlag) {
                if ($dataFlag) { array_unshift($newRowData, $columns);  $sheet->rows($newRowData); }
                else{ $sheet->rows($newRowData); }
                $this->sheetStyle($sheet);
            });

            // 释放变量
            unset($rowData);
            unset($newRowData);
            unset($reportData);

        })->export('xlsx');

        // 刷新输出缓冲到浏览器
        ob_flush();
        flush();
    }

    /**
     * OTC 数据概览导出
     *
     * @param Request $request
     */
    public function report(Request $request)
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
        $reportColumns = ['日期', '累计交易手续费(USDT)','累计充提币手续费(USDT)', '出金溢价收益(USDT)',
            '平台累计收益(USDT)','平台累计收益(RMB)', '累计支出(USDT)', '累计支出(RMB)','收益余额(USDT)','收益余额(RMB)',
            '注册用户','最近7天新增','累计充值数额(USDT)','累计提币数额(USDT)', '累计买入交易数量(USDT)',' 累计卖出交易数量(USDT)',];

        $timeFlag = ($start ?:'开始').'-'.($end ?:'当前');
        if (!($start || $end)) { $timeFlag = Carbon::now()->toDateString(); }
        $fileName = 'OTC数据概览_'.$timeFlag;

        // 处理数据
        $reportData = [];

        // 统计数据概览 - sheet2
        $report = HomeController::exportReport();
        $reportData[0][] = '日期';
        $reportData[0][] = Carbon::now()->toDateString();
        $reportData[1][] = '累计交易手续费(USDT)';
        $reportData[1][] = $report['otcFee'];
        $reportData[2][] = '累计充提币手续费(USDT)';
        $reportData[2][] = $report['walletFee'];
        $reportData[3][] = '出金溢价收益(USDT)';
        $reportData[3][] = $report['otcQuickIncomeSys'];
        $reportData[4][] = '平台累计收益(USDT)';
        $reportData[4][] = $report['otcSysIncomeTotal'];
        $reportData[5][] = '平台累计收益(折合RMB)';
        $reportData[5][] = $report['otcSysIncomeTotalRmb'];
        $reportData[6][] = '累计支出(USDT)';
        $reportData[6][] = $report['otcSysWithdraw'];
        $reportData[7][] = '累计支出(折合RMB)';
        $reportData[7][] = $report['otcSysWithdrawRmb'];
        $reportData[8][] = '收益余额(USDT)';
        $reportData[8][] = $report['otcSysIncomeCurrent'];
        $reportData[9][] = '收益余额(折合RMB)';
        $reportData[9][] = $report['otcSysIncomeCurrentRmb'];
        $reportData[10][] = '注册用户数';
        $reportData[10][] = $report['users'];
        $reportData[11][] = '最近7天新增';
        $reportData[11][] = $report['lastSevenDayUser'];
        $reportData[12][] = '累计充值数额(USDT)';
        $reportData[12][] = $report['otcDepositAmount'];
        $reportData[13][] = '累计提币数额(USDT)';
        $reportData[13][] = $report['otcWithdrawAmount'];
        $reportData[14][] = '累计买入交易数量(USDT)';
        $reportData[14][] = @$report['otcBuyTotal']->field_amount;
        $reportData[15][] = '累计卖出交易数量(USDT)';
        $reportData[15][] = @$report['otcSellTotal']->field_amount;

        // 格式化excel数据
        Excel::create($fileName, function ($excel) use ($reportData, $reportColumns) {
            // 单sheet导出
            $excel->sheet('OTC数据概览', function ($sheet) use ($reportData, $reportColumns){
                //array_unshift($reportData, $reportColumns);
                $sheet->rows($reportData);
                $this->reportSheetStyle($sheet);
            });

            // 释放变量
            unset($reportData);

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
            'F'     =>  25,
        ));

        return $sheet;
    }

    public function reportSheetStyle($sheet)
    {
        // 行格式-首行-背景色-加粗和锁定
        $sheet->row(1, function($rowData) {
            $rowData->setBackground('#00B0F0');
        });

        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        // 列宽
        $sheet->setWidth(array(
            'A'     =>  25,
            'B'     =>  22,
            'C'     =>  25,
            'D'     =>  20,
            'E'     =>  20,
            'F'     =>  20,
            'G'     =>  18,
            'H'     =>  18,
            'I'     =>  19,
            'J'     =>  19,
            'K'     =>  10,
            'L'     =>  12,
            'M'     =>  20,
            'N'     =>  20,
            'O'     =>  25,
            'P'     =>  25,
        ));

        return $sheet;
    }

}
