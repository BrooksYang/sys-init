@extends('entrance::layouts.default')

@section('content')
<div class="box">
    <div class="box-header">

        <h3 class="box-title">
            <span>重置客服密码</span>
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="alert alert-info">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <span class="entypo-info-circled"></span>
        <strong>提醒!</strong>&nbsp;&nbsp;重置密码后，建议客服人员登录系统后自行更新密码
      </div>

    	<div class="row">
	    	<div class="col-md-12">
				<form  accept-charset="utf-8">
					{{ csrf_field() }}
              <div class="form-group">
              <input class="form-control input-lg" type="password" name="password"  id="password" placeholder="请输入新密码" >
              <div id="password_validate"></div>
              </div>              
              <div class="form-group">
              <input class="form-control input-lg" type="password" name="password_confirmation"  id="password_confirmation" placeholder="请确认新密码">
              <div id="re_password_validate"></div>
              </div>  

		          <div class="form-group">
		              <button type="button" class="btn btn-default pull-right" onclick="resetPassword()">提交</button>
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

  function resetPassword(){
      // console.log(CKEDITOR.instances.legal.getData()) 
      var ii = layer.load();
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" } });
      $.ajax({
          type : 'POST',
          url : "{{ url('ticket/supervisor/savePassword').'/'.$id }}",
          dataType : 'json',
          data : {'password':$("#password").val(),'password_confirmation':$("#password_confirmation").val()},  // 获取表单内容
          success : function(data,status){
                // console.log(CKEDITOR.instances.contact_us.getData()) 
                layer.close(ii)
                var obj = eval(data)
                // console.log(obj['success'])
                if (obj['success'] == false) {
                    if(obj['errors']['password'] != null) {
                      $('#password_validate').empty()
                      $('#password_validate').append('<span class="help-block"><strong class="text-danger"> &nbsp; &nbsp;'+obj['errors']['password']+'</strong></span>')
                    } else {
                      $('#password_validate').empty()
                    }
                    if(obj['errors']['password_confirmation'] != null) {
                      $('#re_password_validate').empty()
                      $('#re_password_validate').append('<span class="help-block"><strong class="text-danger"> &nbsp; &nbsp;'+obj['errors']['password_confirmation']+'</strong></span>')
                    } else {
                      $('#re_password_validate').empty()
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
