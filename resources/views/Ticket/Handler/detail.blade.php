@extends('entrance::layouts.default')

@section('content')
    <style>
        .ml-5{
            margin-left: 5px;
        }
    </style>


<div class="box">
    <div class="box-header">
        <div class="pull-right box-tools">
            <a href="#javascript:;" onClick="javascript :history.back(-1);">
                <span class="box-btn"><i class="fa fa-level-up"></i></span>
            </a>
        </div>
        <h3 class="box-title"><i class="fontello-doc"></i>
            <span>工单处理</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="alert alert-warning">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <span class="entypo-cancel-circled"></span>
            <strong>
                操作提示：进行操作前请先仔细核对订单信息、沟通并收集申诉双方所提供相关资料；确认无误后再进行相关操作；
                操作时系统将自动强制处理【已支付-未放币】的订单，请慎重操作！
            </strong>
        </div>
        @if($order ?? '')
        <div class="alert alert-info">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <span class="entypo-info-circled"></span>
            <strong>订单信息：</strong>&nbsp;&nbsp;
            <p><i class="fa fa-user"></i>
                申诉人：{{  ('用户名'.@$ticket->user->username ?:'--').' | 邮箱 '.(@$ticket->user->email?:'--').' | 电话 '.@$ticket->user->phone ?:'--' }}
            </p>
            <P>【订单日期】{{ $order->created_at }}&nbsp;&nbsp;&nbsp;&nbsp;【订单号】{{ $order->id }}</P>
            <p>【广告类型】{{ $order->type_text }}&nbsp;&nbsp;&nbsp;&nbsp;【币种】{{ $order->currency }}&nbsp;&nbsp;&nbsp;&nbsp;
                【法币】{{ $order->legal_currency }}
            </p>
            <p>【广告用户】{{ $order->from }}</p>
            <p>【订单用户】{{ $order->to }}</p>
            <p>【交易数量】{{ $order->field_amount.' '.$order->currency }}&nbsp;&nbsp;&nbsp;&nbsp;【单价】{{ $order->price.' '.$order->legal_currency }}&nbsp;&nbsp;&nbsp;&nbsp;
                【总价】{{ $order->cash_amount.' '.$order->currency }}
            </p>
            <p>【订单状态】{{ $order->status_text }}&nbsp;&nbsp;&nbsp;&nbsp;【申诉状态】{{ $order->appeal_text }}</p>
        </div>
        @endif
    	<div class="row">
	    	<div class="col-md-12">
              <ul class="media-list">
                <li class="media">
                  <div class="media-left">
                    <a href="#">
                      <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="{{ url('img/customer.png') }}" style="width: 64px;">
                    </a>
                  </div>
                  <div class="media-body">
                    <p class="media-heading">【<strong>工单日期：{{ $ticket->created_at }} &nbsp;&nbsp;
                      状态：                        
                        @if($ticket->ticket_state != null)
                        {{ $ticketStatus[$ticket->ticket_state] }}
                        @else
                        未处理
                        @endif</strong>】</p>
                    <p>内容：<b>{{ $ticket->content}}</b></p>
                    @if($role == config('conf.supervisor_role'))
                    <p> <a href="javascript:;" title="回复工单" onclick="ticketReply('{{ $ticket->id }}')">回复</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#javascript:;" onclick="ticketDel('{{ $ticket->id }}')">删除</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{{ url('ticket/handler/ticketTransfer').'/'.$ticket->id }}" title="">转移</a></p>
                    @endif
                    <div class="row" style="margin-top:20px">
                      <div class="col-md-12">
                            @if($ticket->attachment_1_url != null)
                            <a href="{{ url($ticket->attachment_1_url) }}" data-fancybox data-caption="工单编号：{{ $ticket->id }}">
                            <span class="pic"><img  src="{{ url($ticket->attachment_1_url) }}"></span>
                            </a>

                            @endif        
                            @if($ticket->attachment_2_url != null)
                            <a href="{{ url($ticket->attachment_2_url) }}" data-fancybox data-caption="工单编号：{{ $ticket->id }}">
                            <span class="pic"><img  src="{{ url($ticket->attachment_2_url) }}"></span>
                            </a>
                            @endif
                      </div>
                    </div>
                    <!-- Nested media object -->

                    <!-- 工单回复列表展示 -->
                    @foreach($replyMatrix as $reply)
                      @if($reply['levelTwo'] == 0)
                        <div class="media">
                          <div class="media-left">
                            <a href="javascript:;">
                              <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="{{ url('img/supervisor.jpg') }}" data-holder-rendered="true" style="width: 64px; height: 64px;">
                            </a>
                          </div>
                          <div class="media-body">
                            <h6 class="media-heading"><strong>回复时间：{{ $reply['created_at'] }}</strong></h6>
                            {{ $reply['reply_content'] }}


                            <!-- 检查是否有 sub replay，如果有则嵌套显示 -->

                          </div>
                        </div>
                     @else
                        <div class="media">
                          <div class="media-left">
                            <a href="javascript:;">
                              <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="{{ url('img/supervisor.jpg') }}" data-holder-rendered="true" style="width: 64px; height: 64px;">
                            </a>
                          </div>
                          <div class="media-body">
                            <h6 class="media-heading"><strong>回复时间：{{ $reply['created_at'] }}</strong></h6>
                            {{ $reply['reply_content'] }}


                            <!-- 检查是否有 sub replay，如果有则嵌套显示 -->
                            @foreach($reply['levelTwo'] as $replyLtwo)
                              <div class="media" id="reply_cell_{{ $replyLtwo['id'] }}">
                                <div class="media-left">
                                  <a href="#">
                                    @if($replyLtwo['reply_type']==1)
                                    <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="{{ url('img/customer.png') }}" data-holder-rendered="true" style="width: 64px; height: 64px;">
                                    @else
                                    <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="{{ url('img/supervisor.jpg') }}" data-holder-rendered="true" style="width: 64px; height: 64px;">
                                    @endif
                                  </a>
                                </div>
                                <div class="media-body">
                                  <h6 class="media-heading"><strong>回复时间：{{ $replyLtwo['created_at']}} &nbsp;&nbsp; 
                                    @if($replyLtwo['reply_type']==1)
                                    <a href="#javascript:;" onclick="replyLevelTwo('{{ $replyLtwo['ticketId'] }}','{{ $replyLtwo['reply_parent_id'] }}','{{ $replyLtwo['ownerId'] }}')">回复</a>&nbsp;&nbsp;
                                    @endif
                                    <a href="#javascript:;" onclick="deleteReply('{{ $replyLtwo['id'] }}')">删除</a></strong></h6>
                                  {{ $replyLtwo['reply_content'] }}
                                </div>
                              </div>
                            @endforeach 
                          </div>
                        </div>
                     @endif
                    @endforeach
                    @if((isset($order) && $role == config('conf.supervisor_role')) && ($order->appeal_status==\App\Models\OTC\OtcOrder::APPEALING))
                        <div class="pull-right">
                            <a href="javascript:viod(0)" class="btn btn-success" onclick="itemUpdate('{{ $ticket->id }}',
                                    '{{ url("ticket/handler/appealEnd/$ticket->id") }}','normal','{{ $ticket->order_id }}',
                                    '工单为<b><strong> 完结 </strong></b> 状态',
                                    '{{ csrf_token() }}','完结工单');" title="仅更新申诉进程和工单">申诉完结</a>

                            <a href="javascript:viod(0)" class="btn btn-danger ml-5" onclick="itemUpdate('{{ $ticket->id }}',
                                    '{{ url("ticket/handler/appealEnd/$ticket->id") }}','release','{{ $ticket->order_id }}',
                                    '订单为<b><strong> 强制发币 </strong></b> 状态',
                                    '{{ csrf_token() }}','强制发币 - 请慎重操作！');" title="已支付-未放币">强制发币</a>

                           {{-- <a href="javascript:viod(0)" class="btn btn-info ml-5" onclick="itemUpdate('{{ $ticket->id }}',
                                    '{{ url("ticket/handler/appealEnd/$ticket->id") }}','recover','{{ $ticket->order_id }}',
                                    '订单为<b><strong> 强制恢复 </strong></b> 状态',
                                    '{{ csrf_token() }}','强制恢复 - 请慎重操作！');" title="未付款-已放币完成交易 - 强制恢复广告方的放币">强制恢复</a>--}}

                            <a href="javascript:viod(0)" class="btn btn-default ml-5" onclick="itemUpdate('{{ $ticket->id }}',
                                    '{{ url("ticket/handler/appealEnd/$ticket->id") }}','cancel','{{ $ticket->order_id }}',
                                    '订单为<b><strong> 取消 </strong></b> 状态',
                                    '{{ csrf_token() }}','取消订单 - 请慎重操作！');" title="取消OTC订单">取消订单</a>
                        </div>
                    @endif
                  </div>
                </li>
              </ul>
		    </div>
		</div>
    </div>
    <!-- /.box-body -->
</div>
@endsection


@section('js-import')
<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
@endsection

@section('css-import')
<!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css">
@endsection

@section('js-part')

<script type="text/javascript">
  function ticketReply(ticketId) {
    layer.prompt({title: '回复工单内容', formType: 2}, function(content, index){
      layer.close(index); // 关闭窗口
      var ii = layer.load();
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
      $.ajax({
            type : 'POST',
            url : "{{ url('ticket/handler/ticketReply') }}",
            dataType : 'json',
            data : {'ticketId':ticketId,'ownerId':'{{ $ticket->supervisor_id }}','content':content,'replyParentId':0},
            success : function(data,status){
                  layer.close(ii)
                  var obj = eval(data)
                  console.log(obj)
                  if (obj['msg'] == 'success') {
                    layer.msg('回复成功',{icon:1}, function(){
                      $(location).attr('href', "{{ url('ticket/handler/detail').'/' }}"+ticketId);
                    });
                    
                  }
            }

        })
    });
  }


  function ticketDel(ticket_id) {
    var x = layer.confirm('工单和相关的历史回复会一并删除，确定吗？', {
      btn: ['删除','取消'] //按钮
    }, function(){
      var ii = layer.load();
      $.ajax({
          type : 'get',
          url : "{{ url('ticket/handler/delete') }}"+'/'+ticket_id,
          dataType : 'json',
          success : function(data,status){
                layer.close(ii)
                var obj = eval(data)
                if(obj['code']){ layer.msg(data.msg) }
                if(obj['msg'] == 'success') {
                  $(location).attr('href', "{{ url('ticket/handler/index') }}")
                }
          }
      })
      layer.close(x)
    }, function(){
    });
}

  /**
   * 删除二级回复
   * @param  {[type]} reply_id [description]
   * @return {[type]}          [description]
   */
  function deleteReply(reply_id) {
    var x = layer.confirm('确定要删除吗？', {
      btn: ['删除','取消'] //按钮
    }, function(){
      var ii = layer.load();
      $.ajax({
          type : 'get',
          url : "{{ url('ticket/handler/deleteReply') }}"+'/'+reply_id,
          dataType : 'json',
          success : function(data,status){
                layer.close(ii)
                var obj = eval(data)
                if(obj['code']){ layer.msg(data.msg) }
                if(obj['msg'] == 'success') {
                  $('#reply_cell_'+reply_id).remove();
                  // $(location).attr('href', "{{ url('ticket/handler/index') }}")
                }
          }
      })
      layer.close(x)
    }, function(){
    });
}

function replyLevelTwo(ticket_id,parent_id,owner_id) {
    layer.prompt({title: '回复用户工单', formType: 2}, function(content, index){
    layer.close(index); // 关闭窗口
    var ii = layer.load();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
    console.log(content)
    $.ajax({
          type : 'POST',
          url : "{{ url('ticket/handler/replyLevelTwo') }}",
          dataType : 'json',
          data : {'ticket_id':ticket_id,'content':content,'reply_parent_id':parent_id,'owner_id':owner_id},
          success : function(data,status){
                layer.close(ii)
                var obj = eval(data)
                console.log(obj)
                if (obj['msg'] == 'success') {
                  layer.msg('回复成功',{icon:1}, function(){
                    $(location).attr('href', "{{ url('ticket/handler/detail').'/' }}"+ticket_id);
                  });
                }
          }

      })
    });
}

</script>

@endsection

@section('css-part')
<style type="text/css" media="screen">

    .pic {
        display: inline-block;
        width: 100px;
        height: 100px;
        overflow: hidden;
    }
    .pic img {
        max-width: 100px;
        max-height: 100px;
    }
  
</style>
@endsection
