@extends('entrance::layouts.default')

@section('content')
<div class="box">
    <div class="box-header">        
        <h3 class="box-title"><i class="fontello-doc"></i>
            <span>全部工单</span>
        </h3>
        {{-- Filter and Add Button --}}
        <div class="pull-right box-tools" style="margin-right: 20px;">
            @include('component.searchForm', ['url'=>url('ticket/handler/index'), 'placeholder'=>'订单号','placeholderRole'=>'订单号'])
            @include('component.filter', ['url'=>url('ticket/handler/index'), 'filters'=>$type,'filter'=>'type', 'title'=>'筛选类型'])
            @include('component.filter', ['url'=>url('ticket/handler/index'), 'filters'=>$status,'filter'=>'status', 'title'=>'筛选状态',
                   'isInline'=>true,'icon'=>'fontello-th-list'])
        </div>


    </div>
    <!-- /.box-header -->
    <div class="box-body">
    	<div class="row">
	    	<div class="col-md-12">
              <table class="table table-hover table-striped">
                  <tbody>
                  <tr>
                      <th>序号</th>
                      <th>工单编号</th>
                      <th>OTC订单</th>
                      <th>类型</th>
                      <th>所属商户</th>
                      <th>工单摘要</th>
                      <th>当前状态</th>
                      <th>创建日期</th>
                      <th>客服</th>
                      <th>分配时间</th>
                      <th>包含附件</th>
                      @if($role == config('conf.supervisor_role') )
                      <th>操作</th>
                      @endif
                  </tr>
                  @forelse($tickets as $key=>$ticket)
                  <tr id="{{ 'cell_'.$ticket->id }}">
                      <td>{{ ($key + 1) + ($tickets->currentPage() - 1) * $tickets->perPage() }}</td>
                      <td>{{ $ticket->id }}</td>
                      <td>#{{ $ticket->order_id }}</td>
                      <td><span style="color:{{$type[$ticket->order_type]['color']}};">{{$type[$ticket->order_type]['name'] }}</span></td>
                      <td>{{@$ticket->merchant->username ?: @$ticket->merchant->email ?:'--'}}</td>
                      <td><a href="{{ url('ticket/handler/detail').'/'.$ticket->id }}"><?= mb_substr($ticket->content,0,10) ?> ..</a></td>
                      <td>
                        @if($ticket->ticket_state != null)
                        <span class="label label-{{$status[$ticket->ticket_state]['class']}}">{{ $ticketStatus[$ticket->ticket_state] }}</span>
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
                        <a href="####" onclick="ticketDel('{{ $ticket->id }}')">删除</a>&nbsp;&nbsp;

                        <!-- Button trigger modal -->
                        <a href="####"  class="" data-toggle="modal" data-target="#exampleModalReopen{{$key}}" title="开启工单">
                            开启
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalReopen{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalReopen{{$key}}Title"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url("ticket/handler/appealReopen/$ticket->id") }}" role="form" method="POST" >
                                        {{ csrf_field() }}
                                        {{  method_field('PATCH')}}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalReopenTitle{{$key}}"><i class="fontello-warning"></i>开启工单</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <button data-dismiss="alert" class="close" type="button">×</button>
                                                <span class="entypo-cancel-circled"></span>
                                                <strong>操作提示：进行操作前请先仔细核对订单信息并填写订单操作说明以备查</strong>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-12">
                                                        <h4>是否确定重新打开工单?</h4>
                                                        <input type="hidden" name="id" value="{{ $ticket->id }}" >
                                                        <input type="hidden" name="field" value="reopen" >
                                                        <input type="hidden" name="update" value="{{ $ticket->order_id }}" >
                                                        <input type="hidden" name="orderType" value="{{ $ticket->order_type }}" >
                                                        <input class="form-control input-lg" type="text" name="info"
                                                               value="{{ old('info') }}"  placeholder="请填写操所说明" required>
                                                        @if ($errors->has('info'))
                                                            <p class="" style="color: red;"><strong>{{ $errors->first('info') }}</strong></p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                            <button type="submit" class="btn btn-secondary">确定</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                      </td>
                      @endif
                  </tr>
                  @empty
                      <tr><td colspan="{{ $role == config('conf.supervisor_role') ? 12 : 11 }}" class="text-center">
                              <div class="noDataValue">
                                  暂无数据
                              </div>
                          </td></tr>
                  @endforelse
                  </tbody>
            </table>
                {{ $tickets->appends(Request::except('page'))->links() }}
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
