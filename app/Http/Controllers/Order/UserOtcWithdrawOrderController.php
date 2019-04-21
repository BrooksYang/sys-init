<?php

namespace App\Http\Controllers\Order;

use App\Models\OTC\OtcBalance;
use App\Models\OTC\OtcLegalCurrency;
use App\Models\OTC\OtcPayPath;
use App\Models\OTC\OtcWithdraw;
use App\Models\Wallet\SysWallet;
use App\Models\Wallet\UserWallet;
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

        // 提现订单来源
        $from = OtcWithdraw::FROM;

        //按币种-用户名-电话检索
        $search = trim($request->search,'');
        $filterStatus = trim($request->status,'');
        $filterFrom = trim($request->from,'');
        $orderC = trim($request->orderC ?:'desc','');

        // 是否真实提币-真实提币或提人民币
        $userOtcWithdrawOrderQuery = DB::table('otc_withdraws as withdraw')
            ->join('users as u','withdraw.user_id','u.id') //用户信息
            ->join('dcuex_crypto_currency as currency','withdraw.currency_id','currency.id')  //币种
            ->join('otc_pay_paths as otc_pay','withdraw.pay_path_id','otc_pay.id'); //用户线下收款账户

        $select = ['withdraw.id as uid',  'withdraw.*', 'u.username', 'u.phone',
            'currency.currency_title_cn','currency.currency_title_en_abbr',
            'otc_pay.*'];

        if (config('app.otc_withdraw_currency')) {
            $userOtcWithdrawOrderQuery = $userOtcWithdrawOrderQuery
                ->join('dcuex_user_crypto_wallet as u_wallet','withdraw.wallet_id','u_wallet.id'); //用户真实钱包

            $select = ['withdraw.id as uid', 'withdraw.*', 'u.username', 'u.phone',
                'currency.currency_title_cn','currency.currency_title_en_abbr',
                'otc_pay.*',
                'u_wallet.crypto_wallet_title','u_wallet.crypto_wallet_address'];
        }


        $userOtcWithdrawOrder =$userOtcWithdrawOrderQuery
            ->when($search, function ($query) use ($search){
                return $query->where('currency.currency_title_cn','like',"%$search%")
                    ->orwhere('currency.currency_title_en_abbr','like',"%$search%")
                    ->orwhere('u.username', 'like', "%$search%")
                    ->orwhere('u.phone', 'like', "%$search%");
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->where('withdraw.status', $filterStatus);
            })
            ->when($filterFrom, function ($query) use ($filterFrom){
                return $query->where('withdraw.from', $filterFrom);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('withdraw.created_at', $orderC);
            })
            ->select($select)
            ->paginate(self::USER_OTC_WITHDRAW_ORDER_PAGE_SIZE );

        return view('order.userOtcWithdrawOrderIndex', compact('orderStatus', 'from', 'userOtcWithdrawOrder'));
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
       /* $columns = [ 'A用户名', 'B电话', 'C提现时间','D来源' 'E提现USDT金额', 'F汇率','G折合金额(RMB)', 'H手续费百分比(USDT)','I手续费金额(USDT)','J实际到帐金额(RMB)',
            'K收款人','L收款方式','M银行','N账号','O币种','P开户行地址','Q提现状态','R备注'];*/
        $columns = [ '用户名','电话','提现时间','来源','提现USDT金额','汇率','折合金额(RMB)', '手续费百分比(USDT)','手续费金额(USDT)','实际到帐金额(RMB)',
            '收款人','收款方式','银行','账号','币种','开户行地址','提现状态','备注'];

        $timeFlag = ($start ?:'开始').'-'.($end ?:'当前');
        if (!($start || $end)) { $timeFlag = Carbon::now()->toDateString(); }
        $fileName = "用户提现记录_".$timeFlag;

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
                $rowData[$key][] = OtcWithdraw::FROM[$item->from]['name'] ?? '';
                $rowData[$key][] = $item->amount ?? '';
                $rowData[$key][] = $item->rate ?? '';
                $rowData[$key][] = number_format($item->rmb,2,'.','') ?? ''; // 折合金额（RMB）
                $rowData[$key][] = $item->fee_percentage ?? ''; // 手续费百分比（USDT）
                $rowData[$key][] = number_format($item->fee,2,'.','') ?? ''; // 手续费（USDT）
                $rowData[$key][] = number_format(bcsub($item->rmb, bcmul($item->fee, $item->rate)), 2) ?? ''; // 实际到帐金额（RMB）

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
            'A'   =>  10,
            'B'   =>  15,
            'C'   =>  20,
            'D'   =>  6,
            'E'   =>  20,
            'F'   =>  10,
            'G'   =>  20,
            'H'   =>  20,
            'I'   =>  20,
            'J'   =>  20,
            'K'   =>  10,
            'L'   =>  12,
            'M'   =>  20,
            'N'   =>  20,
            'O'   =>  10,
            'P'   =>  15,
            'Q'   =>  12,
            'R'   =>  10,
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
        $exportNum = OtcWithdraw::whereIn('status',[OtcWithdraw::OTC_WAITING, OtcWithdraw::OTC_PENDING, OtcWithdraw::OTC_RELEASED])
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
        $export = OtcWithdraw::whereIn('status',[OtcWithdraw::OTC_WAITING, OtcWithdraw::OTC_PENDING, OtcWithdraw::OTC_RELEASED])
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
     * 更新提现订单及钱包余额  | 包含OTC提现和交易所提现
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = OtcWithdraw::findOrFail($id);
        $balance = OtcBalance::firstOrNew(['user_id' => $order->user_id, 'currency_id' => $order->currency_id]);
        $userWallet = UserWallet::firstOrNew(['user_id' => $order->user_id, 'user_wallet_currency_id' => $order->currency_id]);
        $sysWallet = SysWallet::where('sys_wallet_currency_id', $order->currency_id)->first();
        $from =$order->from;

        //如核对通过则从交易用户的对应记账钱包中提币
        $jsonArray = ['code' =>0, 'msg' => '更新成功' ];
        bcscale(config('app.bcmath_scale'));

        // 已发币
        if ($request->update  == OtcWithdraw::OTC_RELEASED) {
            DB::transaction(function () use ($order,$balance, $userWallet,$from,$sysWallet) {
                //更新提币订单
                $order->status = OtcWithdraw::OTC_RELEASED;
                $order->updated_at = self::carbonNow();
                $order->save();

                if ($from == OtcWithdraw::EX_WITHDRAW) {
                    //更新ex记账钱包余额
                    $userWallet->user_wallet_balance_freeze_amount = bcsub($userWallet->user_wallet_balance_freeze_amount, $order->amount);
                    $userWallet->updated_at = self::carbonNow();
                    $userWallet->save();
                }

                if ($from == OtcWithdraw::OTC_WITHDRAW) {
                    //更新otc记账钱包余额
                    $balance->frozen = bcsub($balance->frozen, $order->amount);
                    $balance->updated_at = self::carbonNow();
                    $balance->save();
                }


               /* $sysWallet->sys_wallet_balance = bcsub($sysWallet->sys_wallet_balance, $order->amount);
                $sysWallet->sys_wallet_balance_freeze_amount  = bcsub($sysWallet->sys_wallet_balance_freeze_amount, $order->amount);
                $sysWallet->updated_at = self::carbonNow();
                $sysWallet->save();*/
            });
        }

        // 失败-恢复冻结金额
        if ($request->update  == OtcWithdraw::OTC_FAILED) {
            DB::transaction(function () use ($order,$balance, $userWallet, $from, $sysWallet) {
                //更新提币订单
                $order->status = OtcWithdraw::OTC_FAILED;
                $order->updated_at = self::carbonNow();
                $order->save();

                if ($from == OtcWithdraw::EX_WITHDRAW) {
                    //更新ex记账钱包余额
                    $userWallet->user_wallet_balance_freeze_amount = bcsub($userWallet->user_wallet_balance_freeze_amount, bcadd($order->amount,$order->fee));
                    $userWallet->user_wallet_balance = bcadd($userWallet->user_wallet_balance, bcadd($order->amount,$order->fee));
                    $userWallet->updated_at = self::carbonNow();
                    $userWallet->save();
                }

                if ($from == OtcWithdraw::OTC_WITHDRAW) {
                    //更新otc记账钱包余额
                    $balance->frozen = bcsub($balance->frozen, bcadd($order->amount,$order->fee));
                    $balance->available = bcadd($balance->available, bcadd($order->amount,$order->fee));
                    $balance->updated_at = self::carbonNow();
                    $balance->save();
                }

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
