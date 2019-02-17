@extends('entrance::layouts.default')

@section('css-import')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/entrance/js/range-slider/jquery.range2dslider.css') }}" />
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑普通交易配置' : '普通交易配置' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("otc/config/1") : url('otc/config') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        @forelse($configs as $key => $config)
                            @if($config->key == $configKey[0])
                            {{-- 支付时限 payment_length--}}
                            <div class="form-group {{ $errors->has($configKey[0]) ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>{{ $config->title }}(分钟)</label>
                                    <p style="margin-bottom: 30px"></p>
                                    <input id="slider4" class="form-control input-lg" type="text" name="{{ $configKey[0] }}" value="{{ $config->value ?? old($configKey[0]) }}"
                                           placeholder="订单支付时限">
                                    @if ($errors->has($configKey[0]))
                                        <span class="help-block"><strong>{{ $errors->first($configKey[0]) }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <p style="margin-bottom: 100px"></p>
                            @endif

                            @if($config->key ==  $configKey[1])
                            {{-- 订单取消频次 order_cancel_frequency --}}
                            <div class="form-group {{ $errors->has($configKey[1]) ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>{{ $config->title }}</label>
                                    <p style="margin-bottom: 30px"></p>
                                    <input id="slider5" class="form-control input-lg" type="text" name="{{$configKey[1]}}" value="{{ $config->value ?? old($configKey[1]) }}"
                                           placeholder="订单取消频次">
                                    @if ($errors->has($configKey[1]))
                                        <span class="help-block"><strong>{{ $errors->first($configKey[1]) }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($key == 'exchange_rate_usdt_rmb')
                            <br/><br/>
                            {{-- USDT对人民币汇率 exchange_rate_usdt_rmb --}}
                            <div class="form-group {{ $errors->has($configKey[2]) ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>{{ $config->title }}</label>
                                    <input class="form-control input-sm" type="text" name="{{$configKey[2]}}" value="{{ $config->value ?? old($configKey[1]) }}"
                                           placeholder="USDT对人民币汇率" required>
                                    @if ($errors->has($configKey[2]))
                                        <span class="help-block"><strong>{{ $errors->first($configKey[2]) }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($loop -> last)
                                {{-- Buttons --}}
                                <p style="margin-bottom: 50px"></p>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        {{--<a href="{{ url('otc/config') }}" class="btn btn-default">返回</a>--}}
                                        <button type="submit" class="btn btn-default pull-right">确定</button>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <div class="noDataValue">
                                暂无数据
                            </div>
                        @endforelse
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/range-slider/jquery.range2dslider.js') }}'></script>
    <script>
        $(function () {
            "use strict";
            //支付时长配置
            $('#slider4').range2DSlider({
                template: 'horizontal',
                value: [
                    ['{{ $configs[$configKey[0]]->value ?? 15 }}', 1]
                ],
                onlyGridPoint: true,
                round: true,
                axis: [
                    [
                        @for($i=1; $i<=config('app.otc_payment_length'); $i==1?$i+=4:$i+=5)
                        {{ $i }},
                        @endfor
                    ]
                ]
            });

            //订单可取消频次
            $('#slider5').range2DSlider({
                template: 'horizontal',
                value: [
                    ['{{ $configs[$configKey[1]]->value ?? 3 }}', 1]
                ],
                onlyGridPoint: true,
                round: true,
                axis: [
                    [
                        @for($i=1; $i<=config('app.order_cancel_max_frequency'); $i++)
                        {{ $i }},
                        @endfor
                    ]
                ]
            });

        })
    </script>
@endsection
