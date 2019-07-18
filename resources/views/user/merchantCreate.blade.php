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

                        {{-- 国家 --}}
                        <div class="row">
                            <div class="col-md-12">
                            <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>国家或地区</label>
                                    <select name="country_id" id="country_id" class="form-control margin-t-15" style="margin-right: 5px;">
                                        @foreach($countries as $key=>$country)
                                            <option value="{{ $country->id }}"
                                                    {{--data-length="{{$country->length}}"--}}
                                                    data-abbr="{{$country->abbr}}"
                                                    @if(@$editFlag)
                                                        {{ $user->country_id == $country->id ? 'selected' :'' }}
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
                                    <input class="form-control input-medium" type="text" name="username" value="{{ $user->username ?? old('username') }}"
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
                                        <input class="form-control input-medium" type="text" name="id_number" value="{{ $user->id_number ?? old('id_number') }}"
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
                                    <input class="form-control input-medium" type="text" name="email" value="{{ $user->email ?? old('email') }}"
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
                                    <input class="form-control input-medium" type="text" name="phone" value="{{ $user->phone ?? old('phone') }}"
                                           placeholder="请填写电话">
                                    @if ($errors->has('phone'))
                                        <span class="help-block"><strong>{{ $errors->first('phone') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>

                        {{--身份证号--}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('id_number') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>绑定IP(可选)</label>
                                        <input class="form-control input-medium" type="text" name="id_number" value="{{ $user->id_number ?? old('id_number') }}"
                                               placeholder="绑定ip后系统生成的key永久有效，未绑定ip过期时间为90天（多个ip使用英文逗号间隔）">
                                        @if ($errors->has('id_number'))
                                            <span class="help-block"><strong>{{ $errors->first('id_number') }}</strong></span>
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
                                                  placeholder="请填写备注信息">{{ $announcement->remark ?? old('remark') }}</textarea>
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
                                <a href="{{ url('user/merchant') }}" class="btn btn-default">返回</a>
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
