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
                                    <strong>认证状态：</strong>{{$user->verify_status ? ($kycStatus[$user->verify_status]['name'] ?? '暂无'):'暂无'}}&nbsp;&nbsp;&nbsp;&nbsp;
                                    <strong>认证等级：</strong>{{ $user->kyc_level_id }}&nbsp;&nbsp;&nbsp;&nbsp;
                                </p>
                            </div>

                            {{--身份证件信息--}}
                            *身份证件信息：<br/><br>
                            <p class="">
                                @if($user->id_image_front ?? $user->id_image_back )
                                <img id="" src="{{ config('app.api_res_url') }}/{{ $user->id_image_front }}" style="width:570px;border-radius:20px"
                                     onerror="this.src='http://placehold.it/570x420'"/>
                                <br>
                                <img id="" src="{{ config('app.api_res_url') }}/{{ $user->id_image_back }}" style="width:570px;border-radius:20px"
                                 onerror="this.src='http://placehold.it/570x420'"/>
                                @else
                                    暂无信息
                                @endif
                            </p>
                            <hr>

                            {{--护照信息--}}
                            *护照信息：<br/><br>
                            <p class="">
                                @if(in_array(pathinfo($user->passport)['extension'] ?? '',['pdf']))
                                    <a href="{{ config('app.api_res_url') }}/{{ $user->passport }}" target="_blank">护照文件：{{ pathinfo($user->passport)['basename'] }}</a>
                                @elseif($user->passport)
                                    <img id="" src="{{ config('app.api_res_url') }}/{{ $user->passport }}" style="width:570px;border-radius:20px"
                                     onerror="this.src='http://placehold.it/570x420'"/>
                                @else
                                    暂无信息
                                @endif
                            </p>
                            <hr>

                            {{--信用卡电子账单--}}
                            *信用卡电子账单：<br><br>
                            <p class="">
                                @if(in_array(pathinfo($user->bill)['extension'] ?? '',['pdf']))
                                    <a href="{{ config('app.api_res_url') }}/{{ $user->bill }}" target="_blank">信用卡电子账单：{{ pathinfo($user->bill)['basename'] }}</a>
                                @elseif($user->bill)
                                    <img id="" src="{{ config('app.api_res_url') }}/{{ $user->bill }}" style="width:570px;border-radius:20px"
                                     onerror="this.src='http://placehold.it/570x420'"/>
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
                                @if(in_array($user->verify_status,[1,2,4]))
                                    <a  class="btn btn-default" style="margin-right: 10px" href="javascript:;" onclick="itemUpdate('{{ $user->id }}',
                                            '{{ url("user/manage/$user->id") }}','verify_status',3,
                                            '用户账号为<b><strong> 认证通过 </strong></b> 状态',
                                            '{{ csrf_token() }}', '认证通过');"> <i class="fontello-ok" title="认证通过"></i>认证通过</a>
                                @elseif($user->verify_status == 3)
                                    <a class="btn btn-default" style="margin-right: 10px" href="javascript:;" onclick="itemUpdate('{{ $user->id }}',
                                            '{{ url("user/manage/$user->id") }}','verify_status',4,
                                            '用户账号为<b><strong> 认证失败 </strong></b> 状态',
                                            '{{ csrf_token() }}', '认证失败');"> <i class="fontello-cancel-circled" title="认证失败"></i>认证失败</a>
                                @endif
                                <a href="{{ URL::previous() }}" class="btn btn-default">返回</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection
