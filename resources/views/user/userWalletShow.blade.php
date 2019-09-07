@extends('entrance::layouts.default')

@section('css-part')
    @parent
    <style>
        .t-left{
            text-align: left;
        }
        .t-right{
            text-align: right;
        }
        table tr{
            height:30px; /*把table标签的行高设定为固定值*/
        }
        table td {
            padding: 8px;
            line-height: 2.428571!important;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>商户钱包账户资料信息</span>
                    </h3>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <i class="fontello-vcard"></i> 【UID】<strong>#{{ $merchant->id }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <i class="fontello-user"></i>【用户名】{{ $merchant->username ?: '--' }}
                        </div>
                        <div class="col-md-4">
                            <i class="fontello-mobile-1"></i>【手机号】<span class="label label-info">{{ $merchant->phone ?:'--' }}</span>
                        </div>
                        <div class="col-md-4">
                            <i class="fontello-mail"></i>【邮箱】{{ $merchant->email ?: '--' }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            【累计广告卖出】<strong>{{ number_format($totalTradesSell, 8) }}</strong>
                        </div>
                        <div class="col-md-4">
                            【商户买入】 <strong>{{ number_format($field, 8)}}</strong>
                        </div>
                        <div class="col-md-4">
                            【商户到账】 <strong>{{ number_format($final, 8) }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            【商户提币】 <strong>{{ number_format($withdraw, 8) }}</strong>
                        </div>
                        <div class="col-md-4">
                            【广告卖出】 <strong>{{ number_format($sell, 8) }}</strong>
                        </div>
                        <div class="col-md-4">
                            【商户出金】 <strong>{{ number_format($out, 8) }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            【商户正常余额】 <strong>{{ number_format($correctBalance, 8) }}</strong>
                        </div>
                        <div class="col-md-4">
                            【商户当前余额】 <strong style="color: #45B6B0">{{ number_format($currentBalance, 8) }}&nbsp;</strong>
                            【冻结】<strong style="color: #FF6B6B">{{ number_format($frozen, 8) }}</strong>
                        </div>
                        <div class="col-md-4">
                            【用户累计充值】 <strong>{{ number_format($totalDeposit, 8) }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            【用户总余额】 <strong style="color: #32526E">{{ number_format($totalBalance, 8) }}</strong>
                        </div>
                        <div class="col-md-4">
                            【广告累计余量】 <strong>{{ number_format($totalLeft, 8) }}</strong>
                        </div>
                    </div>
                    <div style="height: 50px"></div>

                    {{-- Paginaton --}}
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right" style="margin:35px 15px;">
                                <a href="{{ URL::previous() }}" class="btn btn-info">返回</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
