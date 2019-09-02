<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Resources\OtcOrderResource;
use App\Models\OTC\OtcOrder;
use App\Models\OTC\OtcTicket;
use App\Models\OTC\Trade;
use App\Models\OTC\UserAppKey;
use App\Models\Wallet\Balance;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Entrance;

class HandlerController extends Controller
{


    private $supervisor;
    private $admin;
    private $ticketStatus;

    public function __construct()
    {
        bcscale(config('app.bcmath_scale'));

        $this->supervisor = 3;  // 定义工单客服的ROLE ID
        $this->admin = 1; // 定义超管的 ROLE ID
        $this->ticketStatus = [
            1 => '未分配',
            2 => '已分配',
            3 => '已回复',
            4 => '已关闭',
            5 => '正在处理',
            6 => '等待处理'
        ];  // 工单状态字典
    }

    /**
     * 我的任务
     * @return [type] [description]
     */
    public function task()
    {
        $data['status'] = OtcTicket::STATUS;
        $data['ticketStatus'] = json_encode($this->ticketStatus);

        return view('Ticket.Handler.task',$data);
    }

    /**
     * 获取我的任务
     * @return [type] [description]
     */
    public function getTask()
    {

        if(Entrance::user()->role_id == $this->supervisor) {
            $task = DB::table('otc_ticket')
                                ->where('supervisor_id', Entrance::user()->id)
                                ->where('ticket_state',2)
                                ->orWhere('ticket_state',6)
                                ->orderByDesc('created_at')
                                ->get();

        } elseif(Entrance::user()->role_id == $this->admin) {
            $task = DB::table('otc_ticket')->orderByDesc('created_at')->get();
        }

        return response()->json(['task'=>$task]);
    }

    /**
     * 客服进行二级回复
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function replyLevelTwo(Request $request)
    {

        $reply = [
            'ticket_id'=>$request->input('ticket_id'),
            'reply_type'=>0,
            'owner_id'=>$request->input('owner_id'),
            'reply_content'=>$request->input('content'),
            'reply_parent_id'=>$request->input('reply_parent_id'),
            'created_at'=>\Carbon\Carbon::now(),
            'updated_at'=>\Carbon\Carbon::now(),
        ];
        DB::table('otc_ticket_reply')->insert($reply);
        DB::table('otc_ticket')->where('id',$request->input('ticket_id'))->update(['ticket_state'=>3]);


        return response()->json(['msg'=>'success']);

    }

    /**
     * 删除用户回复
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteReply($id)
    {
        if (Entrance::user()->role_id == config('conf.supervisor_role')) {
            return response()->json(['code'=>403, 'msg'=>'暂无权限']);
        }

        $reply = DB::table('otc_ticket_reply')->where('id',$id)->first();

        DB::table('otc_ticket_reply')->where('id',$id)->delete();
        DB::table('otc_ticket')->where('id',$reply->ticket_id)->update(['ticket_state'=>3]);

        return response()->json(['msg'=>'success']);
    }

    /**
     * 删除工单
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function destroy($id)
    {
        if (Entrance::user()->role_id == config('conf.supervisor_role')) {
            return response()->json(['code'=>403, 'msg'=>'暂无权限']);
        }

        DB::transaction(function () use($id) {
            DB::table('otc_ticket')->where('id',$id)->delete();
            DB::table('otc_ticket_reply')->where('ticket_id',$id)->delete();
        });

        return response()->json(['msg'=>'success']);
    }

    /**
     * 客服进行一级回复
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function ticketReply(Request $request)
    {
        $reply = [
            'ticket_id'=>$request->input('ticketId'),
            'owner_id'=>$request->input('ownerId'),
            'reply_content'=>$request->input('content'),
            'reply_parent_id'=>$request->input('replyParentId'),
            'created_at'=>\Carbon\Carbon::now(),
            'updated_at'=>\Carbon\Carbon::now(),
        ];
        DB::table('otc_ticket_reply')->insert($reply);
        DB::table('otc_ticket')->where('id',$request->input('ticketId'))->update(['ticket_state'=>3]); // 修改原始工单的状态为已回复


        return response()->json(['msg'=>'success']);
    }


    /**
     * 工单处理详情页
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $ticket = OtcTicket::with('user')->findOrFail($id);
        $data['ticketStatus'] = $this->ticketStatus;
        $data['ticket'] = $ticket;
        $data['role'] = @Entrance::user()->role_id;

        // 判定工单是否属于申诉工单
        if ($ticket->order_id) {
            $data['order'] = $this->orderDetail($ticket->order_id);
        }

        $replyMatrix = DB::table('otc_ticket_reply')
                            ->where('ticket_id',$id)
                            ->where('reply_parent_id',0)
                            ->get();

        $replyGroup = [];
        //一级回复
        foreach ($replyMatrix as $reply) {
            // 二次回复不存在的情况
            if ( DB::table('otc_ticket_reply')->where('reply_parent_id', $reply->id)->exists()==false ) {
                $levelOne = [
                    'id' => $reply->id,
                    'ticketId' => $reply->ticket_id,
                    'ownerId' => $reply->owner_id,
                    'reply_type' => $reply->reply_type,
                    'reply_content' => $reply->reply_content,
                    'reply_parent_id' => $reply->reply_parent_id,
                    'created_at' => $reply->created_at,
                    'levelTwo' => 0,
                ];
            } else {
                // 二级回复存在则获取相关的内容
                $res = DB::table('otc_ticket_reply')
                                        ->where('ticket_id',$id)
                                        ->where('reply_parent_id',$reply->id)
                                        ->get();
                $levelTwo = [];
                foreach($res as $replyLtwo) {
                    $tmp = [
                        'id' => $replyLtwo->id,
                        'ticketId' => $replyLtwo->ticket_id,
                        'ownerId' => $replyLtwo->owner_id,
                        'reply_type' => $replyLtwo->reply_type,
                        'reply_content' => $replyLtwo->reply_content,
                        'reply_parent_id' => $replyLtwo->reply_parent_id,
                        'created_at' => $replyLtwo->created_at,
                        'levelThree' => 0,
                    ];
                    array_push($levelTwo,$tmp);
                }

                $levelOne = [
                    'id' => $reply->id,
                    'ticketId' => $reply->ticket_id,
                    'ownerId' => $reply->owner_id,
                    'reply_type' => $reply->reply_type,
                    'reply_content' => $reply->reply_content,
                    'reply_parent_id' => $reply->reply_parent_id,
                    'created_at' => $reply->created_at,
                    'levelTwo' => $levelTwo,
                ];
            }

            array_push($replyGroup, $levelOne);
        }

        // dd($replyGroup);
        $data['replyMatrix'] = $replyGroup;
        return view('Ticket.Handler.detail',$data);
    }

    /**
     * 全部工单，用于管理员对工单的管理分配.
     *
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $data['status'] = OtcTicket::STATUS;
        $data['search'] = $search;
        $data['ticketStatus'] = $this->ticketStatus;
        $data['role'] = Entrance::user()->role_id;

        if(Entrance::user()->role_id == $this->supervisor) {
            $data['tickets'] = DB::table('otc_ticket')
                                ->when($search, function ($query) use ($search){
                                    return $query->where('order_id', $search);
                                })
                                ->where('supervisor_id', Entrance::user()->id)
                                ->orderByDesc('created_at')
                                ->paginate('30');

        } elseif(Entrance::user()->role_id == $this->admin) {
            $data['tickets'] = DB::table('otc_ticket')
                ->when($search, function ($query) use ($search){
                    return $query->where('order_id', $search);
                })
                ->orderByDesc('created_at')->paginate('30');
        }




        return view('Ticket.Handler.index',$data);
    }

    /**
     * 返回工单客服的账号信息
     * @param  [type] $supervisorId [description]
     * @return [type]               [description]
     */
    public function supervisor($supervisorId)
    {
        $supervisor = DB::table('auth_admins')->where('id',$supervisorId)->first();

        return response()->json([
            'email'=>$supervisor->email,
            'name'=>$supervisor->name,
        ]);
    }

    /**
     * 工单转移操作页面
     * @param  [type] $ticketId [description]
     * @return [type]           [description]
     */
    public function ticketTransfer($ticketId)
    {
        $data['supervisorList'] = DB::table('otc_supervisor_state')
                    ->join('auth_admins','otc_supervisor_state.supervisor_id','=','auth_admins.id')
                    ->where('otc_supervisor_state.live_state',0)
                    ->where('otc_supervisor_state.active_state',0)
                    ->get();
                    // dd($data['supervisorList']);
        $data['ticketId'] = $ticketId;
        $data['ticket'] = DB::table('otc_ticket')->where('id',$ticketId)->first();
        $data['ticketStatus'] = $this->ticketStatus;

        return view('Ticket.Handler.transfer',$data);
    }

    /**
     * 执行工单转移
     * @param  [type] $supervisorId [description]
     * @return [type]               [description]
     */
    public function transfer(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $transferTo = $request->input('transferTo');
        $transferFrom = $request->input('transferFrom');
        DB::table('otc_ticket')->where('id',$ticketId)->update(['supervisor_id'=>$transferTo]); // 更新工单
        $transferToCount = DB::table('otc_ticket')->where('supervisor_id',$transferTo)->count(); // 查数
        $transferFromCount = DB::table('otc_ticket')->where('supervisor_id',$transferFrom)->count(); // 查数
        DB::table('otc_supervisor_state')->where('supervisor_id',$transferTo)->update(['ticket_amount'=>$transferToCount]);
        DB::table('otc_supervisor_state')->where('supervisor_id',$transferFrom)->update(['ticket_amount'=>$transferFromCount]);

        return response()->json(['msg'=>'success']);
    }

    /**
     * 订单详情
     *
     * @param $id
     * @return mixed
     */
    public function orderDetail($id)
    {
        // 判断订单是否存在
        $order = OtcOrder::findOrFail($id);

        // 字段映射
        $order =  OtcOrderResource::attribute($order);

        return (object)$order;
    }

    /**
     * 申诉处理 - 强制执行放币-取消-恢复等操作
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function appealEnd(Request $request)
    {
        $ticket = OtcTicket::findOrFail($request->id);
        $order = OtcOrder::findOrFail($request->update);

        // 订单存在且为申诉处理中
        if ($order->appeal_status != OtcOrder::APPEALING) {
            abort('400', '非法请求');
        }

        $msg = '更新成功';

        // 更新otc订单及工单
        DB::transaction(function () use($request, $order, $ticket){

            $orcStatus = '[订单原状态_'.OtcOrder::$statusTexts[$order->status].']';
            $remark = $orcStatus.' - '.'申诉完结';

            // 更新otc订单状态及余额
            // 已支付或取消-未放币 - 强制出售方放币
            if ($request->field == 'release' && $order->status == OtcOrder::PAID) {
                $remark = $orcStatus.' - '.'强制放币';
                $msg = $this->forceRelease($order);
            }

            // 已支付-未收到付款 - 强制取消订单
            if ($request->field == 'cancel' && $order->status == OtcOrder::PAID) {
                $remark = $orcStatus.' - '.'取消订单';
                $msg = $this->forceCancel($order);
            }

            // 未付款-已放币完成交易 - 强制恢复广告方的"错误"放币
            if ($request->field == 'recover' && $order->status == OtcOrder::RECEIVED) {
                $remark = $orcStatus.' - '.'强制恢复';
                $msg = $this->forceRecover($order);
            }

            // 仅完结申诉订单和工单

            // 更新otc订单的申诉状态
            $order->appeal_status = OtcOrder::APPEAL_END;
            $order->save();

            // 更新工单状态
            $ticket->ticket_state = OtcTicket::REPLIED;
            $ticket->remark = $remark.' - '."[说明：{$request->info}]";
            $ticket->save();
        });

        return back();
    }

    /**
     * 强制放币（出售方）-（修改为放币后直接完成交易-不再确认收币）
     *
     * @param $order
     * @return mixed
     * @throws \Throwable
     */
    public function forceRelease($order)
    {
        DB::transaction(function () use ($order) {

            bcscale(config('app.bcmath_scale'));

            // 订单原为已取消状态-增加广告已交易数量
           /* if ($order->status == OtcOrder::CANCELED) {
                // 更新广告进度
                $trade = Trade::lockForUpdate()->find($order->advertisement_id);
                $trade->field_amount = bcadd($trade->field_amount, $order->field_amount);
                $trade->field_order_count ++;
                $trade->field_percentage = floor($trade->field_amount / $trade->amount * 10000) / 100;;

                // 若已完成，且撤单，则标记广告为进行中
                if ($trade->status == Trade::FINISHED) {
                    $trade->status = Trade::ON_SALE;
                }
            }*/

            // 用户购买，则发布者->用户，用户出售，则用户->发布者
            $buyerId = $order->type == OtcOrder::BUY ? $order->user_id : $order->from_user_id;
            $sellerId = $order->type == OtcOrder::BUY ? $order->from_user_id : $order->user_id;

            // 是否为商户下用户(商户下用户需更新商户钱包)
            $buyer = User::find($buyerId);
            $merchant = @$buyer->merchantAppKey->user;
            $buyerId = $merchant->id ?? $buyer->id;

            $balanceBuyer = Balance::lockForUpdate()->firstOrNew(['user_id' => $buyerId, 'user_wallet_currency_id' => $order->currency_id]);
            $balanceSeller = Balance::lockForUpdate()->firstOrNew(['user_id' => $sellerId, 'user_wallet_currency_id' => $order->currency_id]);

            // 购买者增加余额 -（该余额为实际到账金额）
            $balanceBuyer->user_wallet_balance = bcadd($balanceBuyer->user_wallet_balance, $order->final_amount);
            $balanceBuyer->save();

            // 出售者减少冻结金额
            $balanceSeller->user_wallet_balance_freeze_amount = bcsub($balanceSeller->user_wallet_balance_freeze_amount, $order->field_amount);
            $balanceSeller->save();

            // 标记为已发币 - 订单完成
            $order->status = OtcOrder::RECEIVED;
            $order->save();
        });

        return '已强制放币';
    }

    /**
     * 强制取消订单
     *
     * @param $order
     * @return string
     * @throws \Throwable
     */
    public function forceCancel($order)
    {
        DB::transaction(function () use ($order) {

            bcscale(config('app.bcmath_scale'));

            /**
             * ***************************************
             * 币商买单，用户卖，则需解冻用户相应金额
             * ***************************************
             */

            if ($order->type == OtcOrder::SELL) {

                // 是否为商户下用户(商户下用户需更新商户钱包)
                $seller = User::find($order->user_id);
                $merchant = @$seller->merchantAppKey->user;
                $sellerId = $merchant->id ?? $seller->id;

                $balance = Balance::where('user_id', $sellerId)
                    ->where('user_wallet_currency_id', $order->currency_id)
                    ->lockForUpdate()
                    ->first();
                $balance->user_wallet_balance_freeze_amount = bcsub($balance->user_wallet_balance_freeze_amount, $order->field_amount);
                $balance->user_wallet_balance = bcadd($balance->user_wallet_balance, $order->field_amount);
                $balance->save();
            }


            /**
             * ***************************************
             * 币商卖单，用户买，则需解冻广告方相应金额
             * ***************************************
             */

            // 取消订单
            $order->status = OtcOrder::CANCELED;
            $order->save();

            // 还原广告进度
            $trade = Trade::lockForUpdate()->find($order->advertisement_id);
            $trade->field_amount = bcsub($trade->field_amount, $order->field_amount);
            $trade->field_order_count --;
            $trade->field_percentage = floor($trade->field_amount / $trade->amount * 10000) / 100;;

            // 若已完成，且撤单，则标记广告为进行中
            if ($trade->status == Trade::FINISHED) {
                $trade->status = Trade::ON_SALE;
            }

            $trade->save();
        });

        return '已强制取消订单';
    }

    /**
     * 强制恢复广告方的"错误"放币
     *
     * @param $order
     * @return string
     * @throws \Throwable
     */
    public function forceRecover($order)
    {
        DB::transaction(function () use($order) {

            bcscale(config('app.bcmath_scale'));

            /**
             * ***************************
             * 处理订单及广告
             * ***************************
             */
            // 取消订单
            $order->status = OtcOrder::CANCELED;
            $order->save();

            // 还原广告进度
            $trade = Trade::lockForUpdate()->find($order->advertisement_id);
            $trade->field_amount = bcsub($trade->field_amount, $order->field_amount);
            $trade->field_order_count --;
            $trade->field_percentage = floor($trade->field_amount / $trade->amount * 10000) / 100;;

            // 若已完成，且撤单，则标记广告为进行中
            if ($trade->status == Trade::FINISHED) {
                $trade->status = Trade::ON_SALE;
            }

            $trade->save();

            /**
             * ***************************
             * 处理广告方及用户钱包余额
             * ***************************
             */
            // 用户购买，则发布者->用户，用户出售，则用户->发布者
            $buyerId = $order->type == OtcOrder::BUY ? $order->user_id : $order->from_user_id;
            $sellerId = $order->type == OtcOrder::BUY ? $order->from_user_id : $order->user_id;

            // 是否为商户下用户(商户下用户需更新商户钱包)
            $buyer = User::find($buyerId);
            $merchant = @$buyer->merchantAppKey->user;
            $buyerId = $merchant->id ?? $buyer->id;

            $balanceBuyer = Balance::lockForUpdate()->firstOrCreate(['user_id' => $buyerId, 'user_wallet_currency_id' => $order->currency_id]);
            $balanceSeller = Balance::lockForUpdate()->firstOrCreate(['user_id' => $sellerId, 'user_wallet_currency_id' => $order->currency_id]);

            // 恢复广告方钱包冻结余额（所恢复数额为订单交易数额）
            $balanceSeller->user_wallet_balance_freeze_amount = bcadd($balanceSeller->user_wallet_balance_freeze_amount, $order->field_amount);
            $balanceSeller->save();

            // 扣除商户钱包可用余额(所扣数额为实际到账金额)
            $balanceBuyer->user_wallet_balance = bcsub($balanceBuyer->user_wallet_balance, $order->final_amount);
            $balanceBuyer->save();
        });

        return '已强制恢复完成交易的订单';
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
        //
    }

}
