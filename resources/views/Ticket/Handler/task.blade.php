@extends('entrance::layouts.default')

@section('content')
<div class="box">
    <div class="box-header">
        
        <h3 class="box-title"><i class="fontello-doc"></i>
            <span>我的待办</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
    	<div class="row">
	    	<div class="col-md-12">
              <table class="table table-hover table-striped" >
                  <tbody id="tasks">
                  <tr>
                      <th>工单编号</th>
                      <th>内容</th>
                      <th>当前状态</th>
                      <th>创建日期</th>
                      <th>分配时间</th>
                      <th>操作</th>
                  </tr>

                  </tbody>
            </table>

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
        var msg = "工单和相关的历史回复会一并删除，确定要删除吗？";
        if(confirm(msg) == true) {
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
                      $('#ticket_'+ticket_id).remove();
                    }
              }
          })
        } else {
          return false
        }
    }


    $(document).ready(function($) {
       var ticketState = ['未分配','已分配','已回复','已关闭','正在处理','等待处理']

        /* START OF ETH 交易订单实时数据 */
        setInterval(function(){
            $.ajax({
              type:'GET',
              url : "{{ url('ticket/handler/getTask') }}",
              dataType: 'json',
              success: function(data,status){
                // console.log(data.task)

                for (var i = 0; i < data.task.length; i++) {
                  // console.log(data.task[i]['content'])
                  var tasks=document.getElementById("tasks");
                  var ticket = document.createElement("tr")
                  ticket.id = "ticket_"+data.task[i]['id']
                  if(data.task[i]['ticket_state']==6)
                    $("#ticket_"+data.task[i]['id']).html("<td>"+data.task[i]['id']+"<td><a href='{{ url('ticket/handler/detail').'/' }}"+data.task[i]['id']+"'>"+data.task[i]['content'].substr(0,20)+"<strong class='text-danger'> (有新回复) </strong></a></td>"+"<td>"+ticketState[data.task[i]['ticket_state']-1]+"</td>"+"<td>"+data.task[i]['created_at']+"</td>"+"<td>"+data.task[i]['assign_at']+"</td>"+"<td><a href='javascript:;' onclick='"+data.task[i]['id']+"'>删除</a></td>")
                  else
                    $("#ticket_"+data.task[i]['id']).html("<td>"+data.task[i]['id']+"<td><a href='{{ url('ticket/handler/detail').'/' }}"+data.task[i]['id']+"'>"+data.task[i]['content'].substr(0,20)+"</a></td>"+"<td>"+ticketState[data.task[i]['ticket_state']-1]+"</td>"+"<td>"+data.task[i]['created_at']+"</td>"+"<td>"+data.task[i]['assign_at']+"</td>"+"<td><a href='javascript:;' onclick='ticketDel("+data.task[i]['id']+")'>删除</a></td>")

                  tasks.appendChild(ticket);  
                }
              }
            })
        },1000)

        /* END OF ETH 交易订单实时数据 */


    });

</script>

@endsection
