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

                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover {{--table-striped--}}">
                       {{-- <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>--}}

                        <tr>
                            <td class="t-right"><i class="fontello-vcard"></i> 【UID】</td>
                            <td colspan="8" class="t-left"><strong>#{{ $merchant->id }}</strong></td>
                        </tr>
                        <tr>
                            <td class="t-right"><i class="fontello-user"></i>【用户名】</td>
                            <td colspan="2" class="t-left">{{ $merchant->username ?: '--' }}</td>
                            <td class="t-right"><i class="fontello-mobile-1"></i>【手机号】</td>
                            <td colspan="2" class="t-left"><span class="label label-info">{{ $merchant->phone ?:'--' }}</span></td>
                            <td class="t-right"><i class="fontello-mail"></i>【邮箱】</td>
                            <td colspan="2" class="t-left">{{ $merchant->email ?: '--' }}</td>
                        </tr>
                        <tr>
                            <td class="t-right">累计广告卖出</td>
                            <td colspan="2" class="t-left">{{ number_format($totalTradesSell,8) }}</td>
                            <td class="t-right">商户买入</td>
                            <td colspan="2" class="t-left">{{ number_format($field,8)}}</td>
                            <td class="t-right">商户到账</td>
                            <td colspan="2" class="t-left">{{ number_format($final,8) }}</td>
                        </tr>
                        <tr>
                            <td class="t-right">商户提币</td>
                            <td colspan="2" class="t-left">{{ number_format($withdraw,8) }}</td>
                            <td class="t-right">广告卖出</td>
                            <td colspan="2" class="t-left">{{ number_format($sell,8) }}</td>
                            <td class="t-right" title="">商户出金</td>
                            <td colspan="2" class="t-left">{{ number_format($out,8) }}</td>
                        </tr>
                        <tr>
                            <td class="t-right" title="（商户正常余额）">商户正常余额</td>
                            <td colspan="2" class="t-left">{{ number_format($correctBalance,8) }}</td>
                            <td class="t-right" title="（商户当前余额）">商户当前余额</td>
                            <td colspan="2" class="t-left">{{ number_format($currentBalance,8) }}&nbsp;【冻结】{{ number_format($frozen,8) }}</td>
                            <td class="t-right">用户累计充值</td>
                            <td colspan="2" class="t-left">{{ number_format($totalDeposit,8) }}</td>
                        </tr>
                        <tr>
                            <td class="t-right" title="（用户总余额）">用户总余额</td>
                            <td colspan="2" class="t-left">{{ number_format($totalBalance) }}</td>
                            <td class="t-right" title="（广告累计余量)">广告累计余量</td>
                            <td colspan="2" class="t-left">{{ number_format($totalLeft,8) }}</td>
                            <td class="t-right"></td>
                            <td colspan="2" class="t-left"></td>
                        </tr>

                    </table>

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
