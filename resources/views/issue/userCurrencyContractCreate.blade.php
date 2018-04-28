@extends('entrance::layouts.default')

@section('css-import')
    {{--<link rel="stylesheet" href="{{ url('/vendor/entrance/js/datepicker/bootstrap-datetimepicker.min.css') }}">--}}
@show


@section('js-import')
   {{-- <script src="{{ url('/vendor/entrance/js/datepicker/bootstrap-datetimepicker.js') }}"></script>--}}
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑用户代币交易合约' : '创建用户代币交易合约' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("issuer/userCurrencyContract/$userCurrencyContract->id") : url('issuer/userCurrencyContract') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{--选择币种--}}
                            <div class="col-md-12">
                                <label>选择代币</label>
                                <div class="form-group {{ $errors->has('currency_id') ? 'has-error' : '' }}" id="typeForm"
                                     style="display: {{ (old('type') || @$permission->group_id) ? 'none' : 'block' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control" name="currency_id" required>
                                            <option value="">请选择代币</option>
                                            @foreach($currency as $key => $item)
                                                <option value="{{ $item->id }}" {{ (@$userCurrencyContract->currency_id == $item->id|| old('currency_id') == $item->id) ? 'selected' : '' }}>
                                                    {!! $item->currency_title_cn.'&nbsp;&nbsp;('.$item->currency_title_en_abbr.')' !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('currency_id'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            {{-- 每日提币上限 --}}
                            <div class="col-md-4">
                                <label>每日提币上限</label>
                                <div class="form-group {{ $errors->has('user_withdraw_daily_amount_limit') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="user_withdraw_daily_amount_limit" value="{{ $userCurrencyContract->user_withdraw_daily_amount_limit ?? old('user_withdraw_daily_amount_limit') }}"
                                               placeholder="每日提币上限">
                                        @if ($errors->has('user_withdraw_daily_amount_limit'))
                                            <span class="help-block"><strong>{{ $errors->first('user_withdraw_daily_amount_limit') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 每日提币次数上限 --}}
                            <div class="col-md-4">
                                <label>每日提币次数上限</label>
                                <div class="form-group {{ $errors->has('user_withdraw_daily_count_limit') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="user_withdraw_daily_count_limit" value="{{ $userCurrencyContract->user_withdraw_daily_count_limit ?? old('user_withdraw_daily_count_limit') }}"
                                               placeholder="每日提币次数上限">
                                        @if ($errors->has('user_withdraw_daily_count_limit'))
                                            <span class="help-block"><strong>{{ $errors->first('user_withdraw_daily_count_limit') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 提币手续费率 --}}
                            <div class="col-md-4">
                                <label>提币手续费率</label>
                                <div class="form-group {{ $errors->has('user_withdraw_fee_rate') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="user_withdraw_fee_rate" value="{{ $userCurrencyContract->user_withdraw_fee_rate ?? old('user_withdraw_fee_rate') }}"
                                               placeholder="提币手续费率">
                                        @if ($errors->has('user_withdraw_fee_rate'))
                                            <span class="help-block"><strong>{{ $errors->first('user_withdraw_fee_rate') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            {{--最小充值金额--}}
                            <div class="col-md-6">
                                <label>最小充值金额</label>
                                <div class="form-group {{ $errors->has('user_deposit_minimum_amount') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="user_deposit_minimum_amount" value="{{ $userCurrencyContract->user_deposit_minimum_amount ?? old('user_deposit_minimum_amount') }}"
                                               placeholder="最小充值金额">
                                        @if ($errors->has('user_deposit_minimum_amount'))
                                            <span class="help-block"><strong>{{ $errors->first('user_deposit_minimum_amount') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--每日卖出限额--}}
                            <div class="col-md-6">
                                <label>每日卖出限额</label>
                                <div class="form-group {{ $errors->has('user_sell_daily_limit') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="user_sell_daily_limit" value="{{ $userCurrencyContract->user_sell_daily_limit ?? old('user_sell_daily_limit') }}"
                                               placeholder="每日卖出限额">
                                        @if ($errors->has('user_sell_daily_limit'))
                                            <span class="help-block"><strong>{{ $errors->first('user_sell_daily_limit') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('issuer/userCurrencyContract') }}" class="btn btn-default">返回</a>
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
