@extends('entrance::layouts.default')

@section('content')
<div class="box">
    <div class="box-header">

        <h3 class="box-title">
            <span>添加工单客服</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="alert alert-info">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <span class="entypo-info-circled"></span>
        <strong>提醒!</strong>&nbsp;&nbsp;工单客服创建后默认密码为：123456 ，请客服人员登录系统后自行修改密码
      </div>
    	<div class="row">
	    	<div class="col-md-12">
				<form  accept-charset="utf-8" id="supervisor">
					{{ csrf_field() }}
              <div class="form-group">
              <input class="form-control input-lg required" type="text" name="name"  id="name" placeholder="请输入用户名">
              <div id="name_validate"></div>
              </div>              
              <div class="form-group">
              <input class="form-control input-lg required email" type="text" name="email"  id="email" placeholder="请输入邮箱地址">
              <div id="email_validate"></div>
              </div>              
		          <div class="form-group">
		              <button type="button" class="btn btn-default pull-right" onclick="postSupervisor()">提交</button>
		          </div>
				</form>
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

  function postSupervisor(){
      // console.log(CKEDITOR.instances.legal.getData()) 
      var ii = layer.load();
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
      $.ajax({
          type : 'POST',
          url : "{{ url('ticket/supervisor/store') }}",
          dataType : 'json',
          data : {'name':$("#name").val(),'email':$("#email").val()},  // 获取表单内容
          success : function(data,status){
                // console.log(CKEDITOR.instances.contact_us.getData()) 
                layer.close(ii)
                var obj = eval(data)
                // console.log(obj['success'])
                if (obj['success'] == false) {
                    if(obj['errors']['name'] != null) {
                      $('#name_validate').empty()
                      $('#name_validate').append('<span class="help-block"><strong class="text-danger"> &nbsp; &nbsp;'+obj['errors']['name']+'</strong></span>')
                    } else {
                      $('#name_validate').empty()
                    }
                    if(obj['errors']['email'] != null) {
                      $('#email_validate').empty()
                      $('#email_validate').append('<span class="help-block"><strong class="text-danger"> &nbsp; &nbsp;'+obj['errors']['email']+'</strong></span>')
                    } else {
                      $('#name_validate').empty()
                    }
                } else if(obj['success'] == true) {

                    layer.msg('保存成功',{icon:1},function(){
                      $(location).attr('href', "{{ url('ticket/supervisor/index') }}");
                    })
                }
          }

      })
}

</script>
@endsection
