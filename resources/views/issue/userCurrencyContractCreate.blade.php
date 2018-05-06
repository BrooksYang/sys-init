@extends('entrance::layouts.default')

@section('css-part')
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/select2/css/select2.min.css') }}">
    <style>
        .select2-selection.select2-selection--multiple, .select2-container--default.select2-container--focus .select2-selection--multiple{
            border-top: 0;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            border-color: rgba(0,0,0,.12);
        }
        .select2-container .select2-search--inline .select2-search__field{
            padding-top: 7px;
            padding-bottom: 7px;
        }
        .text-messages {
            left: 0;
            top: 100%;
        }
        .text-input-danger, .text-input-danger:focus {
            border: 1px solid #ff5b5b !important;
            box-shadow: none;
        }
    </style>
@endsection



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
                                                <option value="{{ $item->id }}"  data-quote-currency="{{$item->currency_title_en_abbr}}" {{ (@$userCurrencyContract->currency_id == $item->id || old('currency_id') == $item->id || !$editFlag) ? 'selected' : '' }}>
                                                    {!! $item->currency_title_cn.'&nbsp;&nbsp;('.$item->currency_title_en_abbr.')' !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('currency_id'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_id') }}</strong></span>
                                        @endif
                                        <input type="hidden" value="" id="quote_currency" name="quote_currency">
                                        <input type="hidden" value="{{ $item->currency_title_en_abbr }}" id="base_currency">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--选择币种交易对--}}
                        <div class="row">
                            <div class="col-md-12">
                                <label>选择交易对</label>
                                <div class="form-group {{ $errors->has('quote_currency') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <select  class="form-control js-example-basic-multiple" id="symbol" multiple name='symbol[]' required>
                                            @foreach(config('app.symbol') as $flag => $symbol)
                                            <option>{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if ($errors->has('quote_currency'))
                                    <span class="help-block" style="color: #a94442"><strong>{{ $errors->first('quote_currency') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{--<div class="row">
                            --}}{{--挂单手续费--}}{{--
                            <div class="col-md-6">
                                <label>挂单手续费</label>
                                <div class="form-group {{ $errors->has('maker_fee') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="maker_fee" value="{{ $userCurrencyContract->maker_fee ?? old('maker_fee') }}"
                                               placeholder="挂单手续费">
                                        @if ($errors->has('maker_fee'))
                                            <span class="help-block"><strong>{{ $errors->first('maker_fee') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            --}}{{--吃单手续费--}}{{--
                            <div class="col-md-6">
                                <label>吃单手续费</label>
                                <div class="form-group {{ $errors->has('taker_fee') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="taker_fee" value="{{ $userCurrencyContract->taker_fee ?? old('taker_fee') }}"
                                               placeholder="吃单手续费">
                                        @if ($errors->has('taker_fee'))
                                            <span class="help-block"><strong>{{ $errors->first('taker_fee') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>--}}

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

                        <div class="row">
                            {{--充值提醒信息--}}
                            <div class="col-md-6">
                                <label>充值提醒信息</label>
                                <div class="form-group {{ $errors->has('user_deposit_warning') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="user_deposit_warning" rows="5"
                                                  placeholder="充值提醒信息">{{ $userCurrencyContract->user_deposit_warning ?? old('user_deposit_warning') }}</textarea>
                                        @if ($errors->has('user_deposit_warning'))
                                            <span class="help-block"><strong>{{ $errors->first('user_deposit_warning') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--提币提醒信息--}}
                            <div class="col-md-6">
                                <label>提币提醒信息</label>
                                <div class="form-group {{ $errors->has('user_withdraw_warning') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" name="user_withdraw_warning" rows="5"
                                                  placeholder="提币提醒信息">{{ $userCurrencyContract->user_withdraw_warning ?? old('user_withdraw_warning') }}</textarea>
                                        @if ($errors->has('user_withdraw_warning'))
                                            <span class="help-block"><strong>{{ $errors->first('user_withdraw_warning') }}</strong></span>
                                        @endif
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
    <script type="text/javascript" src="{{ asset('/vendor/entrance/js/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            //select2 初始化
            $(".js-example-basic-multiple").select2({
                placeholder: "请选择选择交易对",
            });

            opp();

            function opp() {
                symbolArr = [
                    @foreach(config('app.symbol') as $key=>$item)
                        @if(!$loop->last)
                            '{{ $item }}',
                        @else
                            '{{ $item }}'
                        @endif
                    @endforeach
                ];
                symbol = $('#symbol');
                symbol.children().remove();
                for (i=0; i<=symbolArr.length-1; i++){
                    $("<option>"+symbolArr[i]+"</option>").appendTo(symbol);
                }

                //select-2 交易对有值--绑定默认默认值
                var symbolStr = '{{ $symbolStr }}';
                if(symbolStr != ''){
                    var symbolStr = symbolStr.split(',');
                    $('#symbol').select2().val(symbolStr).trigger('change');
                }

                //获取被选币种的计价币种信息
                currency = $("select[name='currency_id']");
                dataQuoteCurrency = currency.find("option:selected").attr("data-quote-currency");

                //获取并初始化计价币种信息
                $('#quote_currency').val(dataQuoteCurrency);

                //可选交易对信息中排除自有币种
                $('#symbol option').each(function () {
                    if(dataQuoteCurrency.toLowerCase() == $(this).text()){
                        $(this).remove();
                    }
                });
            }

            //监听币种的 onchange 事件并同步更新计价币种信息
            currency.change(function () {
                $('#quote_currency').val($(this).find("option:selected").attr("data-quote-currency"));
                opp();
            });
        })
    </script>
@endsection
