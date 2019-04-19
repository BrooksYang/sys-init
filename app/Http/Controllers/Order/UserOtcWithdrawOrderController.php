<?php

namespace App\Http\Controllers\Order;

use App\Models\OTC\OtcBalance;
use App\Models\OTC\OtcLegalCurrency;
use App\Models\OTC\OtcPayPath;
use App\Models\OTC\OtcWithdraw;
use App\Models\Wallet\SysWallet;
use App\Traits\GetWeek;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


/**
 * Class UserOtcWithdrawOrderController
 * @package App\Http\Controllers\Order
 * OTC 用户提币订单
 */
class UserOtcWithdrawOrderController extends Controller
{
    use GetWeek;

    const USER_OTC_WITHDRAW_ORDER_PAGE_SIZE = 20;
    const EXPORT_PERSIZE =100;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 按周浏览导出USDT提现记录
        if ($request->byWeek == 'withdrawWeekExportExcel') {
            $weeks = $this->getWeeks(date('Y',time()));
            krsort($weeks);

            return view('order.userOtcWithdrawWeekIndex',compact('weeks'));
        }

        //订单状态
        $orderStatus = [
            0 => ['name' => '全部',    'class' => ''],
            1 => ['name' => '待受理',  'class' => 'default'],
            2 => ['name' => '处理中',  'class' => 'primary'],
            3 => ['name' => '已发币',  'class' => 'success'],
            4 => ['name' => '失败',    'class' => 'danger'],
        ];

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filter = trim($request->filter,'');
        $orderC = trim($request->orderC,'');
        $userOtcWithdrawOrder = DB::table('otc_withdraws as withdraw')
            ->join('users as u','withdraw.user_id','u.id') //用户信息
            ->join('dcuex_crypto_currency as currency','withdraw.currency_id','currency.id')  //币种
            ->join('dcuex_user_crypto_wallet as u_wallet','withdraw.wallet_id','u_wallet.id') //用户真实钱包
            ->join('otc_pay_paths as otc_pay','withdraw.pay_path_id','otc_pay.id') //用户线下收款账户
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%");
            })
            ->when($filter, function ($query) use ($filter){
                return $query->where('withdraw.status', $filter);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('withdraw.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('withdraw.created_at', 'desc'); //默认创建时间倒序
            })
            ->select(
                'withdraw.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'otc_pay.*',
                'u_wallet.crypto_wallet_title','u_wallet.crypto_wallet_address')
            ->paginate(self::USER_OTC_WITHDRAW_ORDER_PAGE_SIZE );

        return view('order.userOtcWithdrawOrderIndex', compact('orderStatus', 'userOtcWithdrawOrder'));
    }

    /**
     * 交易用户支付账户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayUserAccount(Request $request)
    {
        $userPayAccount = OtcPayPath::where('user_id', $request->payUserId)
            ->where('account',$request->payAccount)
            ->first()->toArray();

        return response()->json($userPayAccount);

    }

    /**
     * 交易用户支付账户信息-提现记录-导出Excel
     *
     * @param Request $request
     * @throws \Maatwebsite\Excel\Exceptions\LaravelExcelException
     */
    public function exportWithDrawExcel(Request $request)
    {
        $start = $request->start_time ?:'';
        $end = $request->end_time ?:'';

        set_time_limit(0);

        // 设置下载excel文件的headers
        $columns = [ '用户名','电话','提现时间','提现USDT金额','汇率','折合金额(RMB)', '收款人','收款方式','银行','账号','币种','开户行地址','提现状态','备注'];

        $timeFlag = ($start ?:'开始').'-'.($end ?:'当前');
        if (!($start || $end)) { $timeFlag = Carbon::now()->toDateString(); }
        $fileName = "OTC用户提现记录_".$timeFlag;

        // 分批次处理-获取数据总数和设置和计算偏移量
        $num = $this->getExportNum($start, $end);
        $perSize = self::EXPORT_PERSIZE;//每次查询的条数
        $pages   = ceil($num / $perSize);
        bcscale(config('app.bcmath_scale'));

        // 获取用户信息
        $users = $this->users();

        // 获取系统法币信息
        $legalCurrencies = $this->legalCurrencies();

        // 处理数据
        $rowData = [];
        for($i = 1; $i <= $pages; $i++) {
            $list = $this->getUnitExportData($i, $perSize, $start, $end);
            foreach($list as $key=>$item) {
                // 账号信息
                $userPay = $this->userPay($item->user_id, $item->pay_path_id);
                $rowData[$key][] = $users[$item->user_id]['username'] ?? '';
                $rowData[$key][] = $users[$item->user_id]['phone'] ?? '';
                $rowData[$key][] = $item->created_at ?? '';
                $rowData[$key][] = $item->amount ?? '';
                $rowData[$key][] = $item->rate ?? '';
                $rowData[$key][] = number_format($item->rmb,2,'.','') ?? ''; // 折合金额（RMB）

                $rowData[$key][] = $userPay->name ?? '';
                $rowData[$key][] = $userPay->payType->name ?? '';
                $rowData[$key][] = $userPay->bank ?? '';
                $rowData[$key][] = '#'.$userPay->account ?? '';
                $rowData[$key][] = $legalCurrencies[$item->currency_id] ?? '';
                $rowData[$key][] = $userPay->bank_address ?? '';

                $rowData[$key][] = OtcWithdraw::$statusTexts[$item->status]?? '';
                $rowData[$key][] = $userPay->remark;
            }
        }

        unset($users);
        unset($list);

        // 格式化excel数据
        Excel::create($fileName, function ($excel) use ($rowData, $columns) {
            $excel->sheet('提现订单', function ($sheet) use ($rowData,$columns) {
                array_unshift($rowData, $columns);
                $sheet->rows($rowData);
                $this->sheetStyle($sheet);
            });

            // 释放变量
            unset($rowData);

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
            'A'     =>  10,
            'B'     =>  15,
            'C'     =>  20,
            'D'     =>  20,
            'E'     =>  20,
            'F'     =>  20,
            'G'     =>  10,
            'H'     =>  12,
            'I'     =>  20,
            'J'     =>  20,
            'K'     =>  10,
            'L'     =>  15,
            'M'     =>  12,
            'N'     =>  10,
        ));

        return $sheet;
    }


    /**
     * 获取数据总量
     * @param $start
     * @param $end
     * @return int
     */
    public function getExportNum($start='', $end='')
    {
        $exportNum = OtcWithdraw::whereIn('status',[OtcWithdraw::OTC_WAITING, OtcWithdraw::OTC_PENDING])
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at','<=', $end);
            })
            ->count();

        return $exportNum;
    }

    /**
     * 分批获取导出数据
     * @param $i
     * @param $perSize
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function getUnitExportData($i, $perSize, $start, $end)
    {
        $export = OtcWithdraw::whereIn('status',[OtcWithdraw::OTC_WAITING, OtcWithdraw::OTC_PENDING])
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at','>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at','<=', $end);
            })
            ->skip(($i-1)*$perSize)->take($perSize)
            ->get();

        return $export;
    }

    /**
     * 获取用户
     * @return array
     */
    public function users()
    {
        $users =  User::get(['phone','username','id']);

        $userInfo = [];
        foreach ($users as $key => $item) {
            $userInfo[$item->id]['username'] = $item->username;
            $userInfo[$item->id]['phone'] = $item->phone;
        }

        unset($users);

        return $userInfo;
    }

    /**
     * 获取用户收款信息
     * @param $uid
     * @param $payPathId
     * @return \Illuminate\Database\Eloquent\Model|null|object
     */
    public function userPay($uid, $payPathId)
    {
        return OtcPayPath::with('payType')->where('user_id',$uid)->where('id',$payPathId)->first();
    }

    /**
     * 系统法币信息
     * @return array
     */
    public function legalCurrencies()
    {
        return OtcLegalCurrency::all()->pluck('name','id')->toArray();
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
        //
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
        $order = OtcWithdraw::findOrFail($id);
        $balance = OtcBalance::firstOrNew(['user_id' => $order->user_id, 'currency_id' => $order->currency_id]);
        $sysWallet = SysWallet::where('sys_wallet_currency_id', $order->currency_id)->first();

        //如核对通过则从交易用户的对应记账钱包中提币
        $jsonArray = ['code' =>0, 'msg' => '更新成功' ];
        bcscale(config('app.bcmath_scale'));

        // 已发币
        if ($request->update  == OtcWithdraw::OTC_RELEASED) {
            DB::transaction(function () use ($order,$balance,$sysWallet) {
                //更新提币订单
                $order->status = OtcWithdraw::OTC_RELEASED;
                $order->updated_at = self::carbonNow();
                $order->save();

                //更新记账钱包余额
                $balance->frozen = bcsub($balance->frozen, $order->amount);
                $balance->updated_at = self::carbonNow();
                $balance->save();

               /* $sysWallet->sys_wallet_balance = bcsub($sysWallet->sys_wallet_balance, $order->amount);
                $sysWallet->sys_wallet_balance_freeze_amount  = bcsub($sysWallet->sys_wallet_balance_freeze_amount, $order->amount);
                $sysWallet->updated_at = self::carbonNow();
                $sysWallet->save();*/
            });
        }

        // 失败-恢复冻结金额
        if ($request->update  == OtcWithdraw::OTC_FAILED) {
            DB::transaction(function () use ($order,$balance,$sysWallet) {
                //更新提币订单
                $order->status = OtcWithdraw::OTC_FAILED;
                $order->updated_at = self::carbonNow();
                $order->save();

                //更新记账钱包余额
                $balance->frozen = bcsub($balance->frozen, $order->amount);
                $balance->available = bcadd($balance->available, $order->amount);
                $balance->updated_at = self::carbonNow();
                $balance->save();

               /* $sysWallet->sys_wallet_balance = bcadd($sysWallet->sys_wallet_balance, $order->amount);
                $sysWallet->sys_wallet_balance_freeze_amount  = bcsub($sysWallet->sys_wallet_balance_freeze_amount, $order->amount);
                $sysWallet->updated_at = self::carbonNow();
                $sysWallet->save();*/
            });
        }

        // 处理中
        if ($request->update == OtcWithdraw::OTC_PENDING) {
            $order->status = OtcWithdraw::OTC_PENDING;
            $order->updated_at = self::carbonNow();
            $order->save();
        }

        return response()->json($jsonArray);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 200050 ,'error' => '不能删除交易用户 OTC 提币订单']);

        /*if (DB::table('otc_withdraws')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }
}
