@extends('entrance::layouts.default')

@section('css-part')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <i class="fontello-doc"></i>
                        <span>{{ @$editFlag ? '编辑商户' : '添加商户' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <div class="alert alert-info">
                        <button data-dismiss="alert" class="close" type="button">×</button>
                        <span class="entypo-info-circled"></span>
                        <strong>提示信息：</strong>&nbsp;&nbsp;
                        商户创建成功后，系统自动生成对应的秘钥对；商户登录门户后即可查看，登录的默认密码为<b> {{ config('conf.merchant_pwd') }} </b>
                    </div>
                    <form class="form form-horizontal" id="form" role="form" method="POST" action="{{ @$editFlag ? url("user/merchant/$user->id") : url('user/merchant') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">
                        @if(@$editFlag)
                        <input type="hidden" name="user" value="{{ $user->user->id }}">
                        @endif

                        {{-- 商户类型 --}}
                        <div class="row">
                            <div class="col-md-12">
                            <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                        <div class="col-sm-12">
                                            <label>商户类型</label>
                                            <select name="type" id="type" class="form-control margin-t-15" style="margin-right: 5px;">
                                                @foreach($types as $flag=>$type)
                                                    <option value="{{ $flag }}" {{--data-length="{{$country->length}}"--}}
                                                    @if(@$editFlag)
                                                        {{ @$user->type == $flag ? 'selected' :'' }}
                                                    @else
                                                        {{ $type == \App\Models\OTC\UserAppKey::COMMON ? 'selected' : ''}}
                                                    @endif
                                                    >
                                                        {{$type['name']}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label>国家或地区</label>
                                    <select name="country_id" id="country_id" class="form-control margin-t-15" style="margin-right: 5px;">
                                        @foreach($countries as $key=>$country)
                                            <option value="{{ $country->id }}"
                                                    {{--data-length="{{$country->length}}"--}}
                                                    data-abbr="{{$country->abbr}}"
                                                    @if(@$editFlag)
                                                        {{ $user->user->country_id == $country->id ? 'selected' :'' }}
                                                    @else
                                                       {{ $country->id == 1 ? 'selected' : ''}}
                                                    @endif
                                            >
                                                {{ $country->code }} {{$country->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="nationality" id="nationality">
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>用户名</label>
                                    <input class="form-control input-medium" type="text" name="username" value="{{ $user->user->username ?? old('username') }}"
                                           placeholder="请填写用户名">
                                    @if ($errors->has('username'))
                                        <span class="help-block"><strong>{{ $errors->first('username') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('id_number') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>身份证号</label>
                                        <input class="form-control input-medium" type="text" name="id_number" value="{{ $user->user->id_number ?? old('id_number') }}"
                                               placeholder="请填写身份证号">
                                        @if ($errors->has('id_number'))
                                            <span class="help-block"><strong>{{ $errors->first('id_number') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 邮箱 --}}
                        <div class="row">
                            <div class="col-md-6">
                            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>邮箱</label>
                                    <input class="form-control input-medium" type="text" name="email" value="{{ $user->user->email ?? old('email') }}"
                                           placeholder="请填写邮箱">
                                    @if ($errors->has('email'))
                                        <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>

                            <div class="col-md-6">
                            <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>电话</label>
                                    <input class="form-control input-medium" type="text" name="phone" value="{{ $user->user->phone ?? old('phone') }}"
                                           placeholder="请填写电话">
                                    @if ($errors->has('phone'))
                                        <span class="help-block"><strong>{{ $errors->first('phone') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>

                        {{--ip及通道开关--}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('ip') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>绑定IP(可选)</label>
                                        <?php
                                            if(@$editFlag && is_null(json_decode($user->ip, true))){
                                                $ip = json_decode($user->ip, true);
                                                $ip = count($ip) == 1 ? $ip[0] : (count($ip) > 1 ? implode(',', $ip) : null);
                                            }
                                        ?>
                                        <input class="form-control input-medium" type="text" name="ip" value="{{ $ip ?? old('ip') }}"
                                               placeholder="绑定ip后系统生成的key永久有效，未绑定ip过期时间为90天（多个ip使用英文逗号间隔）">
                                        @if ($errors->has('ip'))
                                            <span class="help-block"><strong>{{ $errors->first('ip') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('is_enabled') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>是否开启通道</label>
                                        <select name="is_enabled" id="" class="form-control">
                                            <option value="1"  {{@$editFlag ?
                                                ($user->is_enabled == \App\Models\OTC\UserAppKey::OPEN ? 'selected' : '') : 'selected'}}>开启 - 通道</option>
                                            <option value="0" {{@$editFlag ?
                                                ($user->is_enabled == \App\Models\OTC\UserAppKey::CLOSE ? 'selected' : '') : ''}}>关闭 - 通道</option>
                                        </select>
                                        @if ($errors->has('is_enabled'))
                                            <span class="help-block"><strong>{{ $errors->first('is_enabled') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 过期时间 --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('expired_at') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>过期时间</label>
                                        <div id="datetimepicker1" class="input-group date">
                                            <input class="form-control input-sm" id="expired_at" name="expired_at" size="16" type="text"
                                                   value="{{ $user->expired_at ?? old('expired_at') }}"
                                                   placeholder="请选择过期时间" data-format="yyyy-MM-dd hh:mm:ss"  >
                                            <span class="input-group-addon add-on">
                                            <i style="font-style:normal;" data-time-icon="entypo-clock" data-date-icon="entypo-calendar"> </i></span>
                                        </div>
                                        @if ($errors->has('expired_at'))
                                            <span class="help-block"><strong>{{ $errors->first('expired_at') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 交易开始和结束时间 --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('start_time') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>通道交易开始时间</label>
                                        <input id="start_time" name="start_time" value="{{ $user->start_time ?? old('start_time') }}" data-format="HH:mm"
                                            readonly="" class="form-control" type="text">
                                        @if ($errors->has('start_time'))
                                            <span class="help-block"><strong>{{ $errors->first('start_time') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('end_time') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>通道交易结束时间</label>
                                        <input id="end_time" name="end_time" value="{{ $user->end_time ?? old('end_time') }}" data-format="HH:mm"
                                               readonly="" class="form-control" type="text">
                                        @if ($errors->has('end_time'))
                                            <span class="help-block"><strong>{{ $errors->first('end_time') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--备注--}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('remark') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>备注</label>
                                        <textarea class="form-control" name="remark" rows="5"
                                                  placeholder="请填写备注信息">{{ $user->remark ?? old('remark') }}</textarea>
                                        @if ($errors->has('remark'))
                                            <span class="help-block"><strong>{{ $errors->first('remark') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ URL::previous() }}" class="btn btn-default">返回</a>
                                <button type="submit" class="btn btn-default pull-right">确定</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')

    <script>
        $(function () {

            //日期时间插件
            $('#datetimepicker1').datetimepicker({
                language: 'zh'
            });


            // 初始化timePicker
            $('#start_time').clockface();
            $('#end_time').clockface();
            $('#t2').clockface({
                format: 'HH:mm',
                trigger: 'manual'
            });

            let selectCountry = $('#country_id');
            let nationality = $('#nationality');

            let abbr = selectCountry.find("option:selected").attr("data-abbr");
            nationality.val(abbr);

            selectCountry.change(function () {
                let abbr = selectCountry.find("option:selected").attr("data-abbr");
                nationality.val(abbr);
            });
        })
    </script>
@endsection
