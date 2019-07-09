@extends('entrance::layouts.default')

@section('content')
<div class="box">
    <div class="box-header">        
        <h3 class="box-title"><i class="fontello-doc"></i>
            <span>全部工单</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
    	<div class="row">
	    	<div class="col-md-12">
              <table class="table table-hover table-striped">
                  <tbody>
                  <tr>
                      <th>工单编号</th>
                      <th></th>
                      <th>当前状态</th>
                      <th>创建日期</th>
                      <th>客服</th>
                      <th>分配时间</th>
                      <th>包含附件</th>
                      @if($role == config('conf.supervisor_role') )
                      <th>操作</th>
                      @endif
                  </tr>
                  @foreach($tickets as $ticket)
                  <tr id="{{ 'cell_'.$ticket->id }}">
                      <td>{{ $ticket->id }}</td>
                      <td><a href="{{ url('ticket/handler/detail').'/'.$ticket->id }}"><?= mb_substr($ticket->content,0,10) ?> ..</a></td>
                      <td>
                        @if($ticket->ticket_state != null)
                        {{ $ticketStatus[$ticket->ticket_state] }}
                        @else
                        未处理
                        @endif
                      </td>
                      <td>{{ $ticket->created_at ?:'--' }}</td>
                      <td><a href="javascript:;"  @if($ticket->ticket_state != 1) onclick="getSupervisor('{{ $ticket->supervisor_id }}')" @endif>查看</a></td>
                      <td>{{ $ticket->assign_at ?:'--'}}</td>
                      <td>
                        @if($ticket->attachment_1_url != null)
                        <strong class="text-green">是</strong>
                        @else
                        <strong class="text-danger">否</strong>
                        @endif
                      </td>
                      @if($role == config('conf.supervisor_role') )
                      <td>
                        <a href="{{ url('ticket/handler/detail').'/'.$ticket->id }}">处理</a>&nbsp;&nbsp;
                        <a href="{{ url('ticket/handler/ticketTransfer').'/'.$ticket->id }}">转移</a>&nbsp;&nbsp;
                        <a href="#javascript:;" onclick="ticketDel('{{ $ticket->id }}')">删除</a>
                      </td>
                      @endif
                  </tr>
                  @endforeach
                  </tbody>
            </table>

            {{ $tickets->links() }}
		    </div>
		</div>
    </div>
    <!-- /.box-body -->
</div>
<div id="list" style="display: none">
   
</div>
@endsection


@section('js-import')
@endsection

@section('js-part')

<script type="text/javascript">

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
                if(obj['msg'] == 'success') {
                  $('#cell_'+ticket_id).remove();
                  // $(location).attr('href', "{{ url('ticket/handler/index') }}")
                }
          }
      })
      layer.close(x)
    }, function(){
    });
}

// 显示客服人员账号信息
function getSupervisor(supervisor_id) {
    var ii = layer.load();
    $.ajax({
          type : 'GET',
          url : "{{ url('ticket/handler/supervisor') }}"+'/'+supervisor_id,
          dataType : 'json',
          success : function(data,status){
                layer.close(ii)
                var obj = eval(data)
                console.log(obj)
                // 打开弹出页面
                layer.open({
                  type: 1,
                  title: false, //不显示标题
                  skin: 'layui-layer-rim', //加上边框
                  area: ['', ''], //宽高
                  content: '<div style="margin:15px">'+'<h5> 邮箱：'+obj['email']+'</h5>'+'<h5> 账号：'+obj['name']+'</h5>'+'</div>'
                });
          }

      })
}

</script>

@endsection
