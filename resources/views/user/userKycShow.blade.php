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
                                </p>
                                <p class="">
                                    <strong>【累计充值手续费】</strong>{{ number_format(@$transaction['depositFee'],8) }}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>【累计入金交易手续费】</strong>{{ number_format(@$transaction['sellFee'],8) }}&nbsp;&nbsp;
                                    <strong>【累计出金溢价贡献收益】</strong>{{ number_format(@$transaction['outIncome'],8) }}&nbsp;&nbsp;
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

                    {{-- Paginaton --}}
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right">
                                <a href="{{ url('').$uri }}" class="btn btn-default">返回</a>

                                <!-- Button trigger modal -->
                                <a href="javascript:;"  class="btn btn-default" data-toggle="modal" data-target="#exampleModalLongVerify">
                                    <i class="fontello-ok" title="认证通过"></i>认证通过
                                </a>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModalLongVerify" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongVerify" aria-hidden="true">
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

                                <a href="javascript:;" class="btn btn-default" onclick="itemUpdate('{{ $user->id }}',
                                        '{{ url("user/manage/$user->id") }}','verify_status',4,
                                        '用户账号为<b><strong> 认证失败 </strong></b> 状态',
                                        '{{ csrf_token() }}', '认证失败');"> <i class="fontello-cancel-circled" title="认证失败"></i>认证失败</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
