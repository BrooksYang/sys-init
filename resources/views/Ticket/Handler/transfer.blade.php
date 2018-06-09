@extends('entrance::layouts.default')

@section('content')

<div class="box">
  <div class="box-header">
      <h3 class="box-title">
          <span>工单信息</span>
      </h3>
  </div>
  <div class="box-body">
      <div class="row">
        <div class="col-md-12">
          <dl class="dl-horizontal">
            <dt>工单内容：</dt>
            <dd>{{ $ticket->content }}</dd>
            <dt>创建时间：</dt>
            <dd>{{ $ticket->created_at}}</dd>
            <dt>分配时间：</dt>
            <dd>{{ $ticket->assign_at}}</dd>
          </dl>
        </div>
      </div>
  </div>
</div>

<div class="box">
    <div class="box-header">
        
        <h3 class="box-title">
            <span>工单转移</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
    	<div class="row">
	    	<div class="col-md-12">
              <table class="table table-hover">
                  <tbody>
                  <tr>
                      <th></th>
                      <th>账号</th>
                      <th>邮箱</th>
                      <th>状态</th>
                  </tr>
                  @foreach($supervisorList as $supervisor)
                  <tr>
                      <td align="center"><input type="radio" name="transferTo" id="transferTo_{{ $supervisor->supervisor_id }}" value="{{ $supervisor->supervisor_id }}" <?php if($ticket->supervisor_id == $supervisor->supervisor_id) echo " checked ";?>></td>
                      <td>{{ $supervisor->name }}</td>
                      <td>{{ $supervisor->email }}</td>
                      <td>{{ $supervisor->active_state==0?'正常':'被禁用' }}</td>
                  </tr>
                  @endforeach
                  </tbody>
            </table>
            <p class="pull-right" style="margin-top:20px"><button type="" class="btn btn-success" onclick="transferTicket()">转移</button></p>
            
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

  //工单转移
  function transferTicket() {
      var ticketId = '{{ $ticket->id }}'
      var currentOwner = '{{ $ticket->supervisor_id }}'
      var transferTo = $("input[name='transferTo']:checked").val();
      console.log(transferTo)
      layer.confirm('您确定要转移当前的工单给新的客服吗？', {
        btn: ['确定','取消'] //按钮
      }, function(){
        if(currentOwner == transferTo) {
          layer.msg('您不能把工单转移给您自己')
        } else {
              // 开始转移
              var ii = layer.load();
              $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
              $.ajax({
                    type : 'POST',
                    url : "{{ url('ticket/handler/transfer') }}",
                    dataType : 'json',
                    data : {'transferTo':transferTo,'transferFrom':currentOwner,'ticketId':ticketId},
                    success : function(data,status){
                          layer.close(ii)
                          var obj = eval(data)
                          console.log(obj)
                          if (obj['msg'] == 'success') {
                            layer.msg('保存成功',{icon:1}, function(){
                              $(location).attr('href', "{{ url('ticket/handler/index') }}");
                            });
                            
                          }
                    }

                })
        }
      }, function(){
 
      });
  }
</script>

@endsection
