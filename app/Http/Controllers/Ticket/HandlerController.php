<?php

namespace App\Http\Controllers\Ticket;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Entrance;
use App\Jobs\SendSms;

class HandlerController extends Controller
{


    private $supervisor;
    private $admin;
    private $ticketStatus;

    public function __construct()
    {
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
            $task = DB::table('jntz_ticket')
                                ->where('supervisor_id', Entrance::user()->id)
                                ->where('ticket_state',2)
                                ->orWhere('ticket_state',6)
                                ->orderByDesc('created_at')
                                ->get();

        } elseif(Entrance::user()->role_id == $this->admin) {
            $task = DB::table('jntz_ticket')->orderByDesc('created_at')->get();
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
        DB::table('jntz_ticket_reply')->insert($reply);
        DB::table('jntz_ticket')->where('id',$request->input('ticket_id'))->update(['ticket_state'=>3]);


        $userId = DB::table('jntz_ticket')->where('id',$request->input('ticket_id'))->first()->user_id;
        $user = DB::table('jntz_user')->where('id',$userId)->first();
        $code = DB::table('conf_countries')->where('id',$user->country_id)->first()->code;
        
        $phone = $code . @$user->account;

        SendSms::dispatch($phone, "您的工单有新的回复，请及时查看！");
        

        return response()->json(['msg'=>'success']);  

    }

    /**
     * 删除用户回复
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteReply($id)
    {
        $reply = DB::table('jntz_ticket_reply')->where('id',$id)->first();

        DB::table('jntz_ticket_reply')->where('id',$id)->delete();
        DB::table('jntz_ticket')->where('id',$reply->ticket_id)->update(['ticket_state'=>3]);

        return response()->json(['msg'=>'success']); 
    }

    /**
     * 删除工单
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function destroy($id)
    {
        DB::transaction(function () use($id) {
            DB::table('jntz_ticket')->where('id',$id)->delete();
            DB::table('jntz_ticket_reply')->where('ticket_id',$id)->delete();
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
        DB::table('jntz_ticket_reply')->insert($reply);
        DB::table('jntz_ticket')->where('id',$request->input('ticketId'))->update(['ticket_state'=>3]); // 修改原始工单的状态为已回复

        //发送短信
        $userId = DB::table('jntz_ticket')->where('id',$request->input('ticketId'))->first()->user_id;
        $user = DB::table('jntz_user')->where('id',$userId)->first();
        $code = DB::table('conf_countries')->where('id',$user->country_id)->first()->code;
        
        $phone = $code . @$user->account;

        SendSms::dispatch($phone, "您的工单有新的回复，请及时查看！");


        return response()->json(['msg'=>'success']);
    }


    /**
     * 工单处理详情页
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $data['ticketStatus'] = $this->ticketStatus;
        $data['ticket'] = DB::table('jntz_ticket')->where('id',$id)->first();
        $replyMatrix = DB::table('jntz_ticket_reply')
                            ->where('ticket_id',$id)
                            ->where('reply_parent_id',0)
                            ->get();

        $replyGroup = [];
        //一级回复
        foreach ($replyMatrix as $reply) {
            // 二次回复不存在的情况
            if ( DB::table('jntz_ticket_reply')->where('reply_parent_id', $reply->id)->exists()==false ) {
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
                $res = DB::table('jntz_ticket_reply')
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['ticketStatus'] = $this->ticketStatus;
        
        if(Entrance::user()->role_id == $this->supervisor) {
            $data['tickets'] = DB::table('jntz_ticket')
                                ->where('supervisor_id', Entrance::user()->id)
                                ->orderByDesc('created_at')
                                ->paginate('30');

        } elseif(Entrance::user()->role_id == $this->admin) {
            $data['tickets'] = DB::table('jntz_ticket')->orderByDesc('created_at')->paginate('30');
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
        $data['supervisorList'] = DB::table('jntz_supervisor_state')
                    ->join('auth_admins','jntz_supervisor_state.supervisor_id','=','auth_admins.id')
                    ->where('jntz_supervisor_state.live_state',0)
                    ->where('jntz_supervisor_state.active_state',0)
                    ->get();
                    // dd($data['supervisorList']);
        $data['ticketId'] = $ticketId;
        $data['ticket'] = DB::table('jntz_ticket')->where('id',$ticketId)->first();
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
        DB::table('jntz_ticket')->where('id',$ticketId)->update(['supervisor_id'=>$transferTo]); // 更新工单
        $transferToCount = DB::table('jntz_ticket')->where('supervisor_id',$transferTo)->count(); // 查数
        $transferFromCount = DB::table('jntz_ticket')->where('supervisor_id',$transferFrom)->count(); // 查数
        DB::table('jntz_supervisor_state')->where('supervisor_id',$transferTo)->update(['ticket_amount'=>$transferToCount]);
        DB::table('jntz_supervisor_state')->where('supervisor_id',$transferFrom)->update(['ticket_amount'=>$transferFromCount]);

        return response()->json(['msg'=>'success']);
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
