@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑发币方账号' : '初始化发币方账号' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("issuer/issurerInit/$issuer->id") : url('issuer/issurerInit') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{-- Name_cn --}}
                            <div class="col-md-4">
                                <label>发币方中文全称</label>
                                <div class="form-group {{ $errors->has('issuer_title_cn') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="issuer_title_cn" value="{{ $issuer->issuer_title_cn ?? old('issuer_title_cn') }}"
                                               placeholder="发币方中文全称">
                                        @if ($errors->has('issuer_title_cn'))
                                            <span class="help-block"><strong>{{ $errors->first('issuer_title_cn') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Name_en --}}
                            <div class="col-md-4">
                                <label>发币方英文全称</label>
                                <div class="form-group {{ $errors->has('issuer_title_en') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="issuer_title_en" value="{{ $issuer->issuer_title_en ?? old('issuer_title_en') }}"
                                               placeholder="发币方英文全称">
                                        @if ($errors->has('issuer_title_en'))
                                            <span class="help-block"><strong>{{ $errors->first('issuer_title_en') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- abbr_en --}}
                            <div class="col-md-4">
                                <label>发币方英文简写</label>
                                <div class="form-group {{ $errors->has('issuer_title_en_abbr') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="issuer_title_en_abbr" value="{{ $issuer->issuer_title_en_abbr ?? old('issuer_title_en_abbr') }}"
                                               placeholder="发币方英文简称">
                                        @if ($errors->has('issuer_title_en_abbr'))
                                            <span class="help-block"><strong>{{ $errors->first('issuer_title_en_abbr') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                        @if(@$issuerAccountEditFlag)
                        <input type="hidden" name="issuerAccountEditFlag" value="{{ @$issuerAccountEditFlag }}">
                        <div class="row">
                            {{-- issuer --}}
                            <div class="col-md-12">
                                <label>发币方账号</label>
                                <div class="form-group {{ $errors->has('issuer_account') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-sm" type="email" name="issuer" value="{{ $issuer->issuer_account ?? old('issuer_account') }}"
                                               placeholder="发币方账号">
                                        @if ($errors->has('issuer_account'))
                                            <span class="help-block"><strong>{{ $errors->first('issuer_account') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- -pwd --}}
                            <div class="col-md-12">
                                <label>发币方密码</label>
                                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-sm" type="password" name="password" value="{{ $issuer->password ?? old('password') }}"
                                               placeholder="发币方密码">
                                        @if ($errors->has('password'))
                                            <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- confirm pwd --}}
                            <div class="col-md-12">
                                <label>确认密码</label>
                                <div class="form-group {{ $errors->has('repeat_pwd') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-sm" type="password" name="repeat_pwd" value="{{ $issuer->repeat_pwd ?? old('repeat_pwd') }}"
                                               placeholder="再次输入密码">
                                        @if ($errors->has('repeat_pwd'))
                                            <span class="help-block"><strong>{{ $errors->first('repeat_pwd') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            {{-- addr --}}
                            <div class="col-md-6">
                                <label>联系地址</label>
                                <div class="form-group {{ $errors->has('issuer_address') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="issuer_address" value="{{ $issuer->issuer_address ?? old('issuer_address') }}"
                                               placeholder="发币方地址">
                                        @if ($errors->has('issuer_address'))
                                            <span class="help-block"><strong>{{ $errors->first('issuer_address') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- phone --}}
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('issuer_phone') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>联系电话</label>
                                        <input class="form-control input-lg" type="text" name="issuer_phone" value="{{ $issuer->issuer_phone ?? old('issuer_phone') }}"
                                               placeholder="发币方联系电话">
                                        @if ($errors->has('issuer_phone'))
                                            <span class="help-block"><strong>{{ $errors->first('issuer_phone') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
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
