@extends('entrance::layouts.default')

@section('css-part')
    <link href="{{ asset('/css/hbfont/hbfont.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .hbfont [class^="icon-"]:before,
        .hbfont [class*=" icon-"]:before{
            font-family: inherit;
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
                        <span>{{ @$editFlag ? '编辑 OTC 订单支付类型' : '初始化 OTC 订单支付类型' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("otc/payType/$currencyType->id") : url('otc/payType') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>支付类型名称</label>
                                <input class="form-control input-lg" type="text" name="name" value="{{ $currencyType->name ?? old('name') }}"
                                       placeholder="OTC 订单支付类型名称">
                                @if ($errors->has('name'))
                                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Name_en --}}
                        <div class="form-group {{ $errors->has('name_en') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>支付类型英文名称</label>
                                <input class="form-control input-lg" type="text" name="name_en" value="{{ $currencyType->name_en ?? old('name_en') }}"
                                       placeholder="OTC 订单支付类型英文名称">
                                @if ($errors->has('name_en'))
                                    <span class="help-block"><strong>{{ $errors->first('name_en') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- icon --}}
                        <div class="form-group {{ $errors->has('icon') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>支付类型图标</label>
                                <input class="form-control input-lg" type="text" name="icon" value="{{ $currencyType->icon ?? old('icon') }}"
                                       placeholder="OTC 订单支付类型图标(请使用类似fontawsome图标)">
                                @if ($errors->has('icon'))
                                    <span class="help-block"><strong>{{ $errors->first('icon') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{--图标示例--}}
                        <div class="alert alert-info">
                            <button data-dismiss="alert" class="close" type="button">×</button>
                            <span class="entypo-info-circled"></span>
                            <strong>常用图标类型：</strong>&nbsp;&nbsp; 请使用以下常用图标类型或类似fontawsome图标
                        </div>
                        <div id="icons" class="hbfont">
                            <div class="row">
                                <div title="支付宝" class="col-md-3 col-sm-4"><i class="iconfont icon-alipay"></i>
                                    <span class="i-name">iconfont icon-alipay</span>
                                </div>
                                <div title="微信" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-wechat"></i>
                                    <span class="i-name">iconfont icon-wechat</span>
                                </div>
                                <div title="银行卡" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-bankCard"></i>
                                    <span class="i-name">iconfont icon-bankCard</span>
                                </div>
                                <div title="PayPal" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-paypal"></i>
                                    <span class="i-name">iconfont icon-paypal</span>
                                </div>
                                <div title="西联汇款" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-xilian"></i>
                                    <span class="i-name">iconfont icon-xilian</span>
                                </div>
                                <div title="SWIFT国际汇款" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-SWIFT"></i>
                                    <span class="i-name">iconfont icon-SWIFT</span>
                                </div>
                                <div title="新加坡PayNow" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-paynow"></i>
                                    <span class="i-name">iconfont icon-paynow</span>
                                </div>
                                <div title="印度Paytm" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-paytm"></i>
                                    <span class="i-name">iconfont icon-paytm</span>
                                </div>
                                <div title="俄罗斯QIWI" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-QIWI"></i>
                                    <span class="i-name">iconfont icon-QIWI</span>
                                </div>
                                <div title="Interac e-Transter" class="the-icons col-md-3 col-sm-4"><i class="iconfont icon-interac"></i>
                                    <span class="i-name">iconfont icon-interac</span>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('otc/payType') }}" class="btn btn-default">返回</a>
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
