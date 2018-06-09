@extends('entrance::layouts.default')

@section('content')
<div class="box">
    <div class="box-header">
        <div class="pull-right box-tools">
            <a href="{{ url('ticket/supervisor/create') }}">
                <span class="box-btn"><i class="fa fa-plus"></i></span>
            </a>
        </div>
        
        <h3 class="box-title"><i class="fontello-doc"></i>
            <span>工单客服列表</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
    	<div class="row">
	    	<div class="col-md-12">
              <table class="table table-hover table-striped">
                  <tbody>
                  <tr>
                      <th>账号</th>
                      <th>邮箱</th>
                      <th>状态</th>
                      <th>活跃</th>
                      <th>工单数量</th>
                      <th>创建日期</th>
                      <th>最后更新</th>
                      <th>操作</th>
                  </tr>
                  @foreach($res as $supervisor)
                  <tr>
                      <td>{{ $supervisor->name }}</td>
                      <td>{{ $supervisor->email }}</td>
                      <td>
                        @if($supervisor->live_state==0)
                          <span class="label label-success">存活</span>
                        @else
                          <span class="label label-success">已删除</span>
                        @endif
                      </td>
                      <td>
                        @if($supervisor->active_state==0)
                          <span class="label label-success">已开工</span>
                        @else
                          <span class="label label-danger">已休假</span>
                        @endif
                      </td>
                      <td>{{ $supervisor->ticket_amount }}</td>
                      <td>{{ $supervisor->created_at }}</td>
                      <td>{{ $supervisor->updated_at }}</td>
                      <td>
                        @if($supervisor->active_state==0)
                        <a href="javascript:;" onclick="onVacation('{{ $supervisor->supervisor_id }}')">休假</a>&nbsp;&nbsp;
                        @elseif($supervisor->active_state==1)
                        <a href="javascript:;" onclick="backToWork('{{ $supervisor->supervisor_id }}')">开工</a>&nbsp;&nbsp;
                        @endif
                        <a href="{{ url('ticket/supervisor/reset').'/'.$supervisor->supervisor_id }}">重置</a>
                      </td>
                  </tr>
                  @endforeach
                  </tbody>
            </table>

            {{ $res->links() }}
		    </div>
		</div>
    </div>
    <!-- /.box-body -->
</div>
@endsection


@section('js-import')
@endsection

@section('js-part')
<script type="text/javascript">

    function backToWork(supervisorId) {

      layer.confirm('开工后系统将向该客服派发新工单，确认开工吗？', {
          btn: ['确认','取消'] //按钮
        }, function(){
              var ii = layer.load();
              $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
              $.ajax({
                  type : 'POST',
                  url : "{{ url('ticket/supervisor/backToWork') }}",
                  dataType : 'json',
                  data : {'supervisorId':supervisorId},  // 获取休假客服人员ID
                  success : function(data,status){
                        // console.log(CKEDITOR.instances.contact_us.getData()) 
                        layer.close(ii)
                        var obj = eval(data)
                        if (obj['msg'] == 'success') {
                          layer.msg('开工成功',{icon:1}, function(){
                            $(location).attr('href', "{{ url('ticket/supervisor/index') }}");
                          });
                          
                        }
                  }

              })
        }, function(){

        });
    }

  
    function onVacation(supervisorId) {

      layer.confirm('系统将不会向该客服分配新的工单，确认休假吗？', {
          btn: ['确认','取消'] //按钮
        }, function(){
              var ii = layer.load();
              $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
              $.ajax({
                  type : 'POST',
                  url : "{{ url('ticket/supervisor/onVacation') }}",
                  dataType : 'json',
                  data : {'supervisorId':supervisorId},  // 获取休假客服人员ID
                  success : function(data,status){
                        // console.log(CKEDITOR.instances.contact_us.getData()) 
                        layer.close(ii)
                        var obj = eval(data)
                        if (obj['msg'] == 'success') {
                          layer.msg('休假成功',{icon:1}, function(){
                            $(location).attr('href', "{{ url('ticket/supervisor/index') }}");
                          });
                          
                        }
                  }

              })
        }, function(){

        });
    }

</script>
@endsection
