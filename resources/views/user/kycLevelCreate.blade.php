@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <i class="fontello-doc"></i>
                        <span>{{ @$editFlag ? '编辑KYC认证等级类型' : '添加KYC认证等级类型' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("user/kycLevel/manage/$currencyType->id") : url('user/kycLevel/manage') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>KYC等级名称</label>
                                <input class="form-control input-lg" type="text" name="name" value="{{ $currencyType->name ?? old('name') }}"
                                       placeholder="KYC等级名称">
                                @if ($errors->has('name'))
                                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Level --}}
                        <div class="form-group {{ $errors->has('level') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>KYC等级值</label>
                                <input class="form-control input-lg" type="number" min="1" name="level" value="{{ $currencyType->level ?? old('level') }}"
                                       placeholder="KYC等级值">
                                @if ($errors->has('level'))
                                    <span class="help-block"><strong>{{ $errors->first('level') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- withdraw_amount_daily --}}
                        <div class="form-group {{ $errors->has('withdraw_amount_daily') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>单日可提币上限</label>
                                <input class="form-control input-lg" type="number" min="0" name="withdraw_amount_daily" value="{{ $currencyType->withdraw_amount_daily ?? old('withdraw_amount_daily') }}"
                                       placeholder="单日可提币上限">
                                @if ($errors->has('withdraw_amount_daily'))
                                    <span class="help-block"><strong>{{ $errors->first('withdraw_amount_daily') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>等级描述</label>
                                <textarea id="questionContent" class="form-control" name="description" rows="5"
                                          placeholder="等级描述信息">{{ $currencyType->description ?? old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block"><strong>{{ $errors->first('description') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('user/kycLevel/manage') }}" class="btn btn-default">返回</a>
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

        });
    </script>
@endsection
