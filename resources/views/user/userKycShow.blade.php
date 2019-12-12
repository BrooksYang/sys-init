@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户KYC证件信息</span>
                    </h3>
                </div>

                <div class="box-body">
                        <div class="col-lg-12">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <h4>{{ $user->username ?: '' }} KYC资料信息</h4>
                            </div>
                            <p class="text-center">
                                用户名：{{ $user->username ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                注册时间：{{ $user->created_at }}&nbsp;&nbsp;&nbsp;&nbsp;
                                更新时间：{{ $user->updated_at }}&nbsp;&nbsp;&nbsp;&nbsp;
                            </p>
                            <hr>

                            <div class="alert alert-info">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <span class="entypo-info-circled"></span>
                                <strong>个人信息：</strong>
                                <p class="">
                                    <strong>用户名：</strong>{{ $user->username ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                </p>
                                <p class="">
                                    <strong>真实姓名：</strong>{{ $user->full_name ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>性别：</strong>{{ !$user->geder ? '保密' :$user->geder==1 ? '男':'女'  }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>年龄：</strong>{{ $user->age ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                </p>
                                <p class="">
                                    <strong>电话：</strong>{{ $user->phone ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>邮箱：</strong>{{ $user->email ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                </p>
                                <p class="">
                                    <strong>身份证号：</strong>{{ $user->id_number ?: '暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>国家：</strong>{{ $user->country_id ? ($country[$user->country_id] ?? '暂无'):'暂无' }}&nbsp;&nbsp;&nbsp;&nbsp;
                                </p>
                                <p class="">
                                    <strong>认证状态：</strong>{{$user->verify_status ? ($kycStatus[$user->verify_status]['name'] ?? '暂无'):'暂无'}}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>认证等级：</strong>{{ $kycLevels->pluck('name','id')[$user->kyc_level_id] ?? '暂无'}}&nbsp;&nbsp;
                                </p>
                                <br>

                                <p class="">
                                    <strong>【累计充值数额】</strong>{{ number_format(@$transaction['deposit']->amount,8) }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>【累计提币数额】</strong>{{ number_format(@$transaction['withdraw']->amount,8)}}&nbsp;&nbsp;
                                </p>
                                <p class="">
                                    <strong>【累计入金交易量】</strong>{{ number_format(@$transaction['sell']->amount,8) }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>【累计出金交易量】</strong>{{ number_format(@$transaction['out']->amount,8)}}&nbsp;&nbsp;
                                    <strong>【累计团队红利】</strong>{{ number_format(@$transaction['bonusTotal']->amount,8)}}
                                </p>
                                <p class="">
                                    <strong>【累计充值手续费】</strong>{{ number_format(@$transaction['depositFee'],8) }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>【累计入金交易手续费】</strong>{{ number_format(@$transaction['sellFee'],8) }}&nbsp;&nbsp;
                                    <strong>【累计出金溢价贡献收益】</strong>{{ number_format(@$transaction['outIncome'],8) }}&nbsp;&nbsp;
                                </p>
                                <p class="" style="color: red">
                                    <strong>【合计-产生收益】</strong>{{ number_format(@$transaction['contribution'],8) }}
                                </p>
                            </div>
                            <div style="height: 20px"></div>

                            {{--身份证件信息--}}
                            *身份证件信息：<br/><br>
                            <div style="height: 30px"></div>
                            <p class="">
                                @if($user->id_image_front ?? $user->id_image_back )
                                <div class="row">
                                    <div class="col-md-6">
                                        <img id="" src="{{ config('app.api_res_url') }}/{{ $user->id_image_front }}" style="width:270px;border-radius:20px"
                                             onerror="this.src='http://placehold.it/270x200'" onclick="rotate(this)"/>
                                    </div>
                                    <div class="col-md-6">
                                        <img id="" src="{{ config('app.api_res_url') }}/{{ $user->id_image_back }}" style="width:270px;border-radius:20px"
                                             onerror="this.src='http://placehold.it/270x200'" onclick="rotate(this)"/>
                                    </div>
                                </div>


                                <br>

                                @else
                                    暂无信息
                                @endif
                            </p>

                            <hr>

                            {{--手持身份证件信息及护照--}}
                            *手持身份证件照片及护照信息：<br/><br>
                            <div style="height: 30px"></div>
                            <p class="">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if($user->id_image_handheld )
                                            <img id="" src="{{ config('app.api_res_url') }}/{{ $user->id_image_handheld }}" style="width:270px;border-radius:20px"
                                                 onerror="this.src='http://placehold.it/270x200'" onclick="rotate(this)"/>
                                        @else
                                            暂无信息
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if(in_array(pathinfo($user->passport)['extension'] ?? '',['pdf']))
                                            <a href="{{ config('app.api_res_url') }}/{{ $user->passport }}" target="_blank">
                                                {{ pathinfo($user->passport)['basename'] }}</a>
                                        @elseif($user->passport)
                                            <img id="" src="{{ config('app.api_res_url') }}/{{ $user->passport }}" style="width:270px;border-radius:20px"
                                                 onerror="this.src='http://placehold.it/270x200'" onclick="rotate(this)"/>
                                        @else
                                            暂无信息
                                        @endif
                                    </div>
                                </div>

                            </p>
                            <div style="height: 20px"></div>
                            <hr>

                            {{--信用卡电子账单--}}
                            *信用卡电子账单：<br><br>
                            <div style="height: 30px"></div>
                            <p class="">
                                @if(in_array(pathinfo($user->bill)['extension'] ?? '',['pdf']))
                                    <a href="{{ config('app.api_res_url') }}/{{ $user->bill }}" target="_blank">{{ pathinfo($user->bill)['basename'] }}</a>
                                @elseif($user->bill)
                                    <img id="" src="{{ config('app.api_res_url') }}/{{ $user->bill }}" style="width:270px;border-radius:20px"
                                     onerror="this.src='http://placehold.it/270x200'" onclick="rotate(this)"/>
                                @else
                                    暂无信息
                                @endif
                            </p>

                        </div>
                    </div>

                    {{-- 认证及重置账户 --}}
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right" style="margin:35px 15px;">
                                {{--启用/禁用--}}
                                <div class="col-md-2">
                                    @if($user->is_valid == \App\User::ACTIVE)
                                        <a class="btn btn-default" href="####" style="" onclick="itemUpdate('{{ $user->id }}',
                                                '{{ url("user/frozen/$user->id") }}','is_valid', '{{ \App\User::FORBIDDEN }}',
                                                '用户登录账号为 <b><strong>禁用状态</strong></b>',
                                                '{{ csrf_token() }}', '禁用登录账号');"> 禁用登录账号 </a>
                                    @elseif($user->is_valid == \App\User::FORBIDDEN)
                                        <a class="btn btn-success" href="####" style="" onclick="itemUpdate('{{ $user->id }}',
                                                '{{ url("user/frozen/$user->id") }}','is_valid', '{{ \App\User::ACTIVE }}',
                                                '用户登录账号为 <b><strong>启用状态</strong></b>',
                                                '{{ csrf_token() }}', '启用登录账号');"> 启用登录账号 </a>
                                    @endif
                                </div>
                                {{--认证审核--}}
                                <div class="col-md-2">
                                    <!-- Button trigger modal -->
                                    <a href="####"  class="btn btn-default" data-toggle="modal" data-target="#exampleModalLongVerify">
                                        <i class="fontello-ok" title="认证通过"></i>认证通过
                                    </a>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModalLongVerify" tabindex="-1" role="dialog"
                                         aria-labelledby="exampleModalLongVerify" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form role="form" method="POST" action="{{ url("user/manage/$user->id") }}">
                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongVerifyTitle">KYC认证等级</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <label>修改KYC认证等级</label>
                                                        <input type="hidden" name="field" value="kyc_level_id">
                                                        <select name="update" id="" style="width: 80%" required>
                                                            <option value="">请选择认证等级</option>
                                                            @foreach($kycLevels as $flag=>$kycLevel)
                                                                <option value="{{ $kycLevel->id }}" {{ $user->kyc_level_id == $kycLevel->id ? 'selected':'' }} >
                                                                    {{ $kycLevel->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has("kyc_level_id"))
                                                            <span class="help-block" style="color: #a94442"><strong>{{ $errors->first("kyc_level_id") }}</strong></span>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                                        <button type="submit" class="btn btn-secondary" >保存</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="####" class="btn btn-default" onclick="itemUpdate('{{ $user->id }}',
                                            '{{ url("user/manage/$user->id") }}','verify_status','4',
                                            '用户账号为<b><strong> 认证失败 </strong></b> 状态',
                                            '{{ csrf_token() }}', '认证失败');"> <i class="fontello-cancel-circled" title="认证失败"></i>认证失败</a>
                                </div>
                                {{--重置登录及支付--}}
                                <div class="col-md-2">
                                    <!-- Button trigger modal -->
                                    <a href="####" class="btn btn-warning" data-toggle="modal" data-target="#exampleModalLongResetLogin">重置登录密码</a>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModalLongResetLogin" tabindex="-1" role="dialog"
                                         aria-labelledby="exampleModalLongResetLogin" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form role="form" method="POST" action="{{ url("user/manage/info/$user->id") }}">
                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongResetLoginTitle">重置登录密码</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="col-md-12">
                                                                        <p>*系统重置后默认登录密码为 <strong>{{config('conf.def_user_pwd')}}</strong></p>
                                                                        <input class="form-control input-sm" type="text" name="pwd" value="{{old('pwd')??''}}"
                                                                               placeholder="填写新的登录密码（选填）">
                                                                        @if ($errors->has('pwd'))
                                                                            <e class="help-block" style="color: red;"><strong>{{ $errors->first('pwd') }}</strong></e>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                                        <button type="submit" class="btn btn-secondary">确定</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <!-- Button trigger modal -->
                                    <a href="####" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalLongResetPay">重置支付密码</a>
                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModalLongResetPay" tabindex="-1" role="dialog"
                                         aria-labelledby="exampleModalLongResetPay" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form role="form" method="POST" action="{{ url("user/manage/info/$user->id") }}">
                                                {{ csrf_field() }}
                                                {{ method_field('PATCH') }}
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongResetPayTitle">重置支付密码</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="col-md-12">
                                                                        <p>*系统重置后默认支付密码为 <strong>{{config('conf.def_user_pay_pwd')}}</strong></p>
                                                                        <input class="form-control input-sm" type="text" name="paypwd" value="{{old('pwd')??''}}"
                                                                               placeholder="填写新的支付密码（选填）">
                                                                        @if ($errors->has('paypwd'))
                                                                            <e class="help-block" style="color: red;"><strong>{{ $errors->first('paypwd') }}</strong></e>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                                        <button type="submit" class="btn btn-secondary">确定</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ url('').$uri }}" class="btn btn-info">返回</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
        if('{{session('msg')}}'){
            layer.msg('{{session('msg')}}');
            <?php session()->put('msg',''); ?>
        }

        $(function () {
        });
    </script>
@endsection

