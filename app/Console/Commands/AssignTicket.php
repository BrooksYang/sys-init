<?php

namespace App\Console\Commands;

use App\Models\OTC\OtcOrder;
use Illuminate\Console\Command;
use DB;

class AssignTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otc:assignTicket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'assign ticket to supervisor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        // 查询未分配的工单
        $tickets = DB::table('otc_ticket')->whereNull('supervisor_id')->get();
        foreach ($tickets as $ticket) {
            // 有效的最少工单数量（排除被删除和被禁用的客服）
            $min = DB::table('otc_supervisor_state')
                        ->where('live_state',0)
                        ->where('active_state',0)
                        ->min('ticket_amount');

            // 找到最少工单数量的工单客服ID（排除被删除和被禁用的客服）
            $supervisorId = DB::table('otc_supervisor_state')
                                ->where('live_state',0)
                                ->where('active_state',0)
                                ->where('ticket_amount',$min)->first()->supervisor_id;

            DB::transaction(function () use ($ticket, $supervisorId){
                // 为客服分配一个工单
                DB::table('otc_ticket')->where('id',$ticket->id)->update([
                    'supervisor_id'=>$supervisorId,
                    'ticket_state'=>2, //已分配
                    'assign_at'=> \Carbon\Carbon::now(), //分配时间
                ]);
                // 更新工单客服信息表
                $currentTicketAmount = DB::table('otc_ticket')->where('supervisor_id',$supervisorId)->count();
                DB::table('otc_supervisor_state')->where('supervisor_id',$supervisorId)->update([
                    'ticket_amount'=>$currentTicketAmount,
                    'updated_at'=>\Carbon\Carbon::now(),
                ]);

                // 更新otc订单申诉状态
                $otcOrder = OtcOrder::find($ticket->order_id);
                $otcOrder->appeal_status = OtcOrder::APPEALING;
                $otcOrder->save();
            });


        }
    }
}
