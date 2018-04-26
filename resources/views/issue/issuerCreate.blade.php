@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑发币账号' : '初始化发币账号' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("issuer/issurerInit/$issuer->id") : url('issuer/issurerInit') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}

                        {{-- Name_cn --}}
                        <div class="form-group {{ $errors->has('name_cn') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="text" name="name_cn" value="{{ $issuer->name_cn ?? old('name_cn') }}"
                                       placeholder="发币方中文全称">
                                @if ($errors->has('name_cn'))
                                    <span class="help-block"><strong>{{ $errors->first('name_cn') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        {{-- Name_en --}}
                        <div class="form-group {{ $errors->has('name_en') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="text" name="name_en" value="{{ $issuer->name_en ?? old('name_en') }}"
                                       placeholder="发币方英文全称">
                                @if ($errors->has('name_en'))
                                    <span class="help-block"><strong>{{ $errors->first('name_en') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        {{-- abbr_en --}}
                        <div class="form-group {{ $errors->has('abbr_en') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="text" name="abbr_en" value="{{ $issuer->abbr_en ?? old('abbr_en') }}"
                                       placeholder="发币方英文简称">
                                @if ($errors->has('abbr_en'))
                                    <span class="help-block"><strong>{{ $errors->first('abbr_en') }}</strong></span>
                                @endif
                            </div>
                        </div>


                        @if(!$editFlag)
                        <input type="hidden" name="edit_flag" value="{{ @$editFlag }}">
                        {{-- issuer --}}
                        <div class="form-group {{ $errors->has('issuer') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="email" name="issuer" value="{{ $issuer->issuer ?? old('issuer') }}"
                                       placeholder="发币方账号">
                                @if ($errors->has('issuer'))
                                    <span class="help-block"><strong>{{ $errors->first('issuer') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- -pwd --}}
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="password" name="password" value="{{ $issuer->password ?? old('password') }}"
                                       placeholder="发币方密码">
                                @if ($errors->has('password'))
                                    <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- confirm pwd --}}
                        <div class="form-group {{ $errors->has('repeat_pwd') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="password" name="repeat_pwd" value="{{ $issuer->repeat_pwd ?? old('repeat_pwd') }}"
                                       placeholder="再次输入密码">
                                @if ($errors->has('repeat_pwd'))
                                    <span class="help-block"><strong>{{ $errors->first('repeat_pwd') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        @endif
                        {{-- addr --}}
                        <div class="form-group {{ $errors->has('addr') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="text" name="addr" value="{{ $issuer->addr ?? old('addr') }}"
                                       placeholder="发币方地址">
                                @if ($errors->has('addr'))
                                    <span class="help-block"><strong>{{ $errors->first('addr') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        {{-- phone --}}
                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="text" name="phone" value="{{ $issuer->phone ?? old('phone') }}"
                                       placeholder="发币方联系电话">
                                @if ($errors->has('phone'))
                                    <span class="help-block"><strong>{{ $errors->first('phone') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group {{ $errors->has('intro') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <textarea class="form-control" name="intro" rows="5"
                                          placeholder="简介">{{ $issuer->intro ?? old('intro') }}</textarea>
                                @if ($errors->has('intro'))
                                    <span class="help-block"><strong>{{ $errors->first('intro') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('issuer/issurerInit') }}" class="btn btn-default">返回</a>
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
    </script>
@endsection
