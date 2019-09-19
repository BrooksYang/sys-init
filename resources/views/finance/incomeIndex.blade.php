@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 平台收益报表({{ config('conf.currency_usdt') }})</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right" style="margin: 20px 20px;">
                        @include('component.conditionSearch', ['url'=>url('otc/income/daily')])
                        <a href="{{ url('otc/report') }}" style="margin-right:10px;color: #fff;" class="btn btn-info" title="">
                            <i class="fontello-export"></i>&nbsp;&nbsp;导出数据概览
                        </a>
                    </div>

                </div>


                {{-- Table --}}
                <div class="box-body">
                    {{--多条件搜索导出--}}
                    <form action="{{ url('otc/income/daily/export')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--分组/按日、周、月--}}
                            <div class="col-sm-2">
                                <select class="flter-status form-control input-sm" id="searchGroup" name="searchGroup">
                                    @foreach($groups as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('searchGroup')==$key
                                            ? 'selected' : (!Request::get('searchGroup') && $key=='day' ? 'selected':'')}}>{{ $item['name'] }} </option>
                                    @endforeach
                                </select>
                            </div>
                            @include('component.dateTimePicker', ['colMdNum'=>4, 'id'=>1, 'label'=>'','name'=>'start','placeholder'=>'请选择开始时间'])
                            @include('component.dateTimePicker', ['colMdNum'=>4, 'id'=>2, 'label'=>'','name'=>'end','placeholder'=>'请选择结束时间'])
                            <div class="col-sm-2">
                                <div class="input-group date">
                                    <button class="btn btn-default pull-right" title="默认导出全部">按日期导出报表</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>日期</th>
                                <th>交易手续费({{ config('conf.currency_usdt') }})</th>
                                <th>充值手续费({{ config('conf.currency_usdt') }})</th>
                                <th>出金溢价收益({{ config('conf.currency_usdt') }})</th>
                                <th>小计({{ config('conf.currency_usdt') }})</th>
                            </tr>
                            @forelse($otcSysIncome as $key => $item)
                                <tr>
                                    <td>{{ (@$item['key'] + 1) }}</td>
                                    <td>
                                        @if(!Request::get('searchGroup') || Request::get('searchGroup')=='day')
                                            <a href="{{ url('otc/sys/income')}}?start={{@$key.' 00:00:00'}}&end={{@$key.' 59:59:59'}}"
                                               target="_blank">
                                            {{ @$key ?: '--'}}{{ Request::get('searchGroup')=='week' ? ' 周':(Request::get('searchGroup')=='month' ? ' 月':'') }}</a>
                                        @else
                                            {{ @$key ?: '--'}}{{ Request::get('searchGroup')=='week' ? ' 周':(Request::get('searchGroup')=='month' ? ' 月':'') }}
                                        @endif
                                    </td>
                                    <td>{{ floatval(@$item['otc_buy_fee'] ?:0) }}</td>
                                    <td>{{ floatval(@$item['deposit_fee'] ?:0) }}</td>
                                    <td>{{ floatval(@$item['quick_income'] ?:0)}}</td>
                                    <td><strong>{{ floatval(@$item['total'] ?:0) }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">
                                        <div class="noDataValue">
                                            暂无数据
                                        </div>
                                    </td></tr>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                @if($search)
                                    <hr>
                                    <div class="pull-left">
                                        {{--交易总数量，交易总价--}}
                                        总计： <b>{{ number_format($statistics['totals'] ?: 0, 8)}}, &nbsp;共 {{ $otcSysIncome->total() }}</b>&nbsp;条<br>
                                        交易手续费： <b>{{ number_format($statistics['totalBuyFee'] ?: 0, 8)}}</b> |
                                        充值手续费： <b>{{ number_format($statistics['totalDepositFee'] ?: 0, 8) }}</b> |
                                        溢价收益：<b>{{ number_format($statistics['totalQuickIncome'] ?: 0, 8) }} </b>
                                    </div>
                                @endif
                                <div class="pull-right">
                                    {{ $otcSysIncome->appends(Request::except('page'))->links() }}
                                </div>
                            </div>
                        </div>
                        {{-- Paginaton End --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
        $(function () {
            //日期时间插件
            $('#datetimepicker1').datetimepicker({
                language: 'zh'
            });

            //日期时间插件
            $('#datetimepicker2').datetimepicker({
                language: 'zh'
            });

            //按钮搜索
            $('#conditionSearch').click(function () {
                var uri = implodeUri();
                $(this).attr('href', uri);
            });

            // 回车搜索
            $("#searchForm").bind("keypress",function(e){
                // 兼容FF和IE和Opera
                var theEvent = e || window.event;
                var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
                if (code == 13) {
                    console.log('{{ Request::url() }}');
                    e.preventDefault();
                    //回车执行查询
                    var uri = implodeUri();
                    window.location.href = '{{ Request::url() }}'+uri;
                }
            });

            // 整理uri searchTeam
            function implodeUri() {
                var uri = '?searchGroup='+$('#searchGroup').val()
                    /*+'&searchFromUser='+$('#searchFromUser').val()
                    +'&searchRemark='+$('#searchRemark').val()
                    +'&searchCardNumber='+$('#searchCardNumber').val()
                    +'&searchOtc='+$('#searchOtc').val()
                    +'&searchMerchant='+$('#searchMerchant').val()
                    +'&searchCurrency='+$('#searchCurrency').val()
                    +'&filterType='+$('#filterType').val()*/
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }
        })
    </script>
@endsection