@extends('entrance::layouts.default')

@section('css-part')
    <style>
        .sevenDay{color: #32526E;font-size: 22px;font-weight: bold;}
        .soc-widget {
            margin: 0;
            background: rgba(0, 0, 0, 0.1);
            padding: 2px 0;
        }
    </style>
@endsection

@section('content')
    <!-- TC 顶部统计区域 -->
    @if(env('APP_TC_MODULE'))
        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-green">Today</span>
                            </h2>
                            <i class="fontello-shop"></i>
                            <h4 class="text-green">{{ number_format($exchangeOrders,0,'',',') }}</h4>
                            <h5>委托订单数</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>

            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-red">Today</span>
                            </h2>
                            <i class="fontello-ticket"></i>
                            <h4 class="text-red">
                                {{--<small>$</small>--}}{{ number_format($orderLogs,0,'',',') }}</h4>
                            <h5>成交订单数</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-aqua">Today</span>
                            </h2>
                            <i class="fontello-money"></i>
                            <h4 class="text-aqua">{{ number_format($orderAmount,3,'.',',') }}</h4>
                            <h5>成交总额</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    @endif
    <!-- END TC 顶部统计区域 -->

    @if(env('APP_OTC_MODULE'))
        {{--财务预警相关数据--}}
        <div class="row">
            <div class="col-lg-3">
                <div class="content-bg">
                    <div class="content-icon">
                        <i class="fontello-warning-empty bg-red"></i>
                        <h2 class="text-red">{{ number_format($otcToBeWithdrawPending, 2) }}</h2>
                        <p class="text-blue">OTC 提币待处理数额({{ config('conf.currency_usdt') }})&nbsp;&nbsp;&nbsp;&nbsp;
                            <span style="color: #32526E !important;">{{number_format(bcmul($otcToBeWithdrawPending, $rate,8),2)}}</span>(RMB)</p>
                        <hr class="list-unstyled list-inline soc-widget bg-red">
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="content-bg">
                    <div class="content-icon">
                        <i class="fontello-warning-empty bg-aqua"></i>
                        <h2 class="text-aqua">{{ number_format(@$otcSysWithDrawAddrBalance, 2) }}</h2>
                        <p class="text-blue">
                            <a href="https://etherscan.io/address/{{config('blockChain.sys_withdraw_addr')}}" target="_blank">
                                OTC 提币地址余额({{ config('conf.currency_usdt') }})</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <span style="color: #32526E !important;">{{number_format(bcmul(@$otcSysWithDrawAddrBalance, $rate,8),2)}}</span>(RMB)</p>
                        <hr class="list-unstyled list-inline soc-widget bg-red">
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="content-bg">
                    <div class="content-icon">
                        <i class="fontello-warning-empty bg-yellow"></i>
                        <h2 class="text-yellow">{{ number_format($neuCollectPending, 2) }}</h2>
                        <p class="text-blue">OTC 系统待归集数额({{ config('conf.currency_usdt') }})&nbsp;&nbsp;&nbsp;&nbsp;
                            <span style="color: #32526E !important;">{{number_format(bcmul($neuCollectPending, $rate,8),2)}}</span>(RMB)</p>
                        <ul class="list-unstyled list-inline soc-widget bg-yellow">
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="content-bg">
                    <div class="content-icon">
                        <i class="fontello-warning-empty bg-blue"></i>
                        <h2 class="text-blue">{{ number_format($neuCollectionBalance, 2) }}</h2>
                        <p class="text-blue">系统归集账户余额({{ config('conf.currency_usdt') }})&nbsp;&nbsp;&nbsp;&nbsp;
                            <span style="color: #32526E !important;">{{number_format(bcmul($neuCollectionBalance, $rate,8),2)}}</span>(RMB)</p>
                        <ul class="list-unstyled list-inline soc-widget bg-blue">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-green">Current</span>
                            </h2>
                            <i class="fa fa-dollar"></i>
                            <h4 class="text-green">{{ number_format($otcSysDepositAddrBalance, 2) }}</h4>
                            <h5>
                                <a href="https://etherscan.io/address/{{config('blockChain.sys_deposit_addr')}}" target="_blank">
                                    系统储值账户余额({{ config('conf.currency_usdt') }})</a>&nbsp;
                                <span style="color: #32526E !important;">{{number_format(bcmul($otcSysDepositAddrBalance, $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    @endif

    <!-- START Public-1 顶部统计区域 -->
    <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="news-widget">
                        <h2>
                            <span class="bg-blue">Current</span>
                        </h2>
                        <i class="fontello-user-1"></i>
                        <h4 class="text-blue">{{ number_format($users,0,'',',') }}</h4>
                        <h5>当前注册用户数</h5>
                        <div style="clear:both;"></div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

        <div class="col-lg-6">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="news-widget">
                        <h2>
                            <span class="bg-blue">Current</span>
                        </h2>
                        <i class="fontello-user-1"></i>
                        <h4 class="text-blue">{{ number_format($lastSevenDayUser,0,'',',') }}</h4>
                        <h5>最近7天新增用户</h5>
                        <div style="clear:both;"></div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <!-- END Public-1 顶部统计区域 -->

    <!-- OTC 顶部统计区域 -->
    @if(env('APP_OTC_MODULE'))
        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-green">Current</span>
                            </h2>
                            <i class="fa fa-dollar"></i>
                            <h4 class="text-green">{{ number_format($otcDepositAmount, 2) }}</h4>
                            <h5>OTC 累计充值数额({{ config('conf.currency_usdt') }})&nbsp;&nbsp;
                            <span style="color: #32526E !important;">{{number_format(bcmul($otcDepositAmount, $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-red">Current</span>
                            </h2>
                            <i class="fa fa-dollar"></i>
                            <h4 class="text-red">{{ number_format($otcWithdrawAmount, 2) }}</h4>
                            <h5>OTC 累计提币数额({{ config('conf.currency_usdt') }})&nbsp;&nbsp;
                                <span style="color: #32526E !important;">{{number_format(bcmul($otcWithdrawAmount, $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-aqua">Current</span>
                            </h2>
                            <i class="fa fa-dollar"></i>
                            <h4 class="text-aqua">{{ number_format($otcBuyTotal->field_amount, 2) }}</h4>
                            <h5>OTC 累计买入交易数量({{ config('conf.currency_usdt') }})&nbsp;
                                <span style="color: #32526E !important;">{{number_format(bcmul($otcBuyTotal->field_amount, $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-yellow">Current</span>
                            </h2>
                            <i class="fa fa-dollar"></i>
                            <h4 class="text-yellow">{{ number_format($otcSellTotal->field_amount, 2) }}</h4>
                            <h5>OTC 累计卖出交易数量({{ config('conf.currency_usdt') }})&nbsp;
                                <span style="color: #32526E !important;">{{number_format(bcmul($otcSellTotal->field_amount, $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-green">Current</span>
                            </h2>
                            <i class="fa fa-dollar"></i>
                            <h4 class="text-green">{{ number_format(bcadd($otcBuyTotal->fee, $otcSellTotal->fee,8), 2) }}</h4>
                            <h5>OTC  &nbsp;累计交易手续费 ({{ config('conf.currency_usdt') }})&nbsp;
                                <span style="color: #32526E !important;">
                                    {{number_format(bcmul(bcadd($otcBuyTotal->fee, $otcSellTotal->fee,8), $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-green">Current</span>
                            </h2>
                            <i class="fa fa-btc"></i>
                            <h4 class="text-green">{{ number_format(bcadd($transFeeDeposit, $transFeeWithdraw,8), 2) }}</h4>
                            <h5>OTC 累计充提币手续费({{ config('conf.currency_usdt') }})&nbsp;
                                <span style="color: #32526E !important;">
                                    {{number_format(bcmul(bcadd($transFeeDeposit, $transFeeWithdraw,8), $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-aqua">Current</span>
                            </h2>
                            <i class="fa fa-btc"></i>
                            <h4 class="text-aqua">{{ number_format($otcQuickIncomeSys, 2) }}</h4>
                            <h5>OTC 快捷购买溢价收益({{ config('conf.currency_usdt') }})&nbsp;
                                <span style="color: #32526E !important;">{{number_format(bcmul($otcQuickIncomeSys, $rate,8),2)}}</span>(RMB)</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-danger">Current</span>
                            </h2>
                            <i class="fontello-money"></i>
                            <h4 class="text-danger">{{ number_format($grandOtcWithdrawOrder, 2) }}</h4>
                            <h5>OTC 累计提现数额</h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-aqua">Current</span>
                            </h2>
                            <i class="fontello-money"></i>
                            <h4 class="text-aqua"  title="{{$otcSysIncomeTotal}}">{{ number_format($otcSysIncomeTotal, 2) }}</h4>
                            <h5>OTC 平台累计收益({{ config('conf.currency_usdt') }})&nbsp;&nbsp;
                                <span style="color: #32526E !important;">{{number_format($otcSysIncomeRmbTotal,2)}}</span>(RMB)
                            </h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-lg-6">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="news-widget">
                            <h2>
                                <span class="bg-green">Current</span>
                            </h2>
                            <i class="fontello-money"></i>
                            <h4 class="text-green">{{ number_format($otcSysIncomeCurrent, 2) }}</h4>
                            <h5>OTC 收益余额({{ config('conf.currency_usdt') }})&nbsp;&nbsp;
                                <span style="color: #32526E !important;">{{number_format($otcSysIncomeCurrentRmb,2)}}</span>(RMB)
                            </h5>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

    @endif
    <!-- END --OTC 顶部统计区域 -->

    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- START Public-2 统计区域 -->
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- Google auth统计 -->
                            <div id="googleAuth" style="width: 100%;height:300px;"></div>
                        </div>
                        <div class="col-lg-6">
                            <!-- 用户账户状态统计 -->
                            <div id="userEmailPhoneStatus" style="width: 90%;height:300px;"></div>
                        </div>

                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-10">
                            <!-- 认证状态整体分布统计 -->
                            <div id="userVerifyStatus" style="width: 100%;height:300px;"></div>
                        </div>
                    </div>

                    @if(env('APP_OTC_MODULE'))
                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC平台各手续费及收益统计 - 默认USDT -->
                                <div id="otcSysIncomeOfDay" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC平台各商户贡献总收益统计 - 每天 - 默认USDT -->
                                <div id="incomeByMerchantOfDay" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC平台各商户贡献总收益及出入金总额统计 - 默认USDT -->
                                <div id="incomeByMerchant" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC 各商户入金总额统计 - 每天 默认USDT -->
                                <div id="inByMerchantOfDay" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC 各商户入金贡献手续费总额统计 - 每天 默认USDT -->
                                <div id="feeByMerchantOfDay" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC订单卖出及手续费统计 - 默认USDT -->
                                <div id="otcSellOfDay" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC充值及手续费统计 - 默认USDT -->
                                <div id="transFeeDepositOfDay" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>
                    @endif

                    <hr>
                    <div class="row">
                        <div class="col-md-10">
                            <!-- 币种信息统计 -->
                            <div id="currency" style="width: 100%;height:600px;"></div>
                        </div>
                    </div>
                    <!-- END Public-2 统计区域 -->

                    <!-- START TC 统计区域 -->
                    @if(env('APP_TC_MODULE'))
                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- 充值订单状态及金额统计 -->
                                <div id="depositOrderStatus" style="width: 100%;height:400px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- 提币订单数量及统计 -->
                                <div id="withdrawOrderStatus" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- 委托订单数量统计--按状态 -->
                                <div id="exchangeOrderByStatus" style="width: 100%;height:300px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- 委托订单成交数量及金额统计--按类型 -->
                                <div id="exchangeOrderByType" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- 委托订单成交数量及价格统计--按状态 -->
                                <div id="exchangeOrderLog" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>
                    @endif
                    <!-- END TC 统计区域 -->

                    <!-- START OTC 统计区域 -->
                    @if(env('APP_OTC_MODULE'))
                        <hr>
                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC订单成交数量及价格统计--按状态 -->
                                <div id="otcOrder" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-10">
                                <!-- OTC 充值订单成交数量及价格统计--按状态 -->
                                <div id="otcWithdrawOrderStatus" style="width: 100%;height:600px;"></div>
                            </div>
                        </div>
                    @endif
                    <!-- END OTC 统计区域 -->

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
   {{-- <div class="row">
        <div class="box">
            <div class="box-body">
                <div class="col-lg-6">
                    <div id="userEmailPhoneStatus" style="width: 100%;height:400px;"></div>
                </div>
                <div class="col-lg-6">
                    <div id="googleAuth" style="width: 100%;height:400px;"></div>
                </div>
            </div>
        </div>
    </div>--}}

@endsection

@section('js-part')
    <script src="{{ asset('/assets/Echarts/echarts.min.js') }}"></script>
    {{--用户验证状态--}}
    <script type="text/javascript">
        var userEmailPhoneStatus = echarts.init(document.getElementById('userEmailPhoneStatus'));
        var userEmailPhoneStatusOption = {
            title: {
                text: '用户验证状态',
                subtext: '手机及邮箱验证'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['已验证', '未验证']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value',
                boundaryGap: [0, 0.01]
            },
            yAxis: {
                type: 'category',
                //data: ['巴西','印尼','美国','印度','中国','世界人口(万)']
                data: [
                    @foreach($emailPhoneVerifyStatus['yAxis'] as $key => $item)
                        '{{$item }}',
                    @endforeach
                ]
            },
            series: [
                @foreach($emailPhoneVerifyStatus['series'] as $key =>$item)
                {
                    name: '{{ $key }}',
                    type: 'bar',
                    data: [
                        @foreach($item as $k => $v)
                        {{ $v }},
                        @endforeach
                    ]
                },
                @endforeach
            ]
        };
        userEmailPhoneStatus.setOption(userEmailPhoneStatusOption);

    </script>

    {{--Goolge auth 状态--}}
    <script>
        var googleAuth = echarts.init(document.getElementById('googleAuth'));
        var googleAuthOption = {
            title : {
                text: 'Google Auth状态',
                subtext: '人机验证',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['已绑定','绑定未开启','未绑定']
            },
            series : [
                {
                    name: '人机验证',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:[
                        @foreach($googleAuth as $key => $v)
                        {value:{{ $v }}, name:'{{ $key }}'},
                        @endforeach
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        googleAuth.setOption(googleAuthOption);
    </script>

    {{--用户认证状态整体分布--}}
    <script>
        var userVerifyStatus = echarts.init(document.getElementById('userVerifyStatus'));
        var userVerifyStatusOption = {
            title : {
                text: '用户认证状态统计',
                subtext: '认证状态',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                /*x : 'center',
                y : 'bottom',*/
                orient: 'vertical',
                x: 'left',
                data:[
                    @foreach($userVerifyStatus['xAxis'] as $key => $v)
                    '{{ $v }}',
                    @endforeach
                ]
            },
            toolbox: {
                show : false,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {
                        show: true,
                        type: ['pie', 'funnel']
                    },
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            series : [
                {
                    name:'认证状态',
                    type:'pie',
                    radius : [20, 110],
                    center : ['25%', '50%'],
                    roseType : 'radius',
                    label: {
                        normal: {
                            show: false
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    lableLine: {
                        normal: {
                            show: false
                        },
                        emphasis: {
                            show: true
                        }
                    },
                    data:[
                        @foreach($userVerifyStatus['yAxis'] as $key => $v)
                            {value:{{$v}}, name:'{{ $userVerifyStatus['xAxis'][$key] }}'},
                        @endforeach
                    ]
                },
                {
                    name:'认证状态',
                    type:'pie',
                    radius : [30, 110],
                    center : ['75%', '50%'],
                    roseType : 'area',
                    data:[
                        @foreach($userVerifyStatus['yAxis'] as $key => $v)
                            {value:{{$v}}, name:'{{ $userVerifyStatus['xAxis'][$key] }}'},
                        @endforeach
                    ]
                }
            ]
        };
        userVerifyStatus.setOption(userVerifyStatusOption);
    </script>

    @if(env('APP_OTC_MODULE'))
    {{--OTC平台收益统计 - 默认USDT--}}
    <script>
        var otcSysIncomeOfDay = echarts.init(document.getElementById('otcSysIncomeOfDay'));
        var otcSysIncomeOfDayOption = {
            title : {
                text: 'OTC 平台收益',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            toolbox: {
                show : true,
                feature : {
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            legend: {
                data:['订单手续费','充值手续费','溢价收益','总收益']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    data : [@foreach($otcSysIncomeOfDay as $incomeKey=>$incomeItem) '{{date('n/d', strtotime($incomeKey))}}', @endforeach]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'订单手续费',
                    type:'bar',
                    data:[@foreach($otcSysIncomeOfDay as $incomeKey=>$incomeItem) {{round($incomeItem['otc_buy_fee'], 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                {
                    name:'充值手续费',
                    type:'bar',
                    stack: '广告',
                    data:[@foreach($otcSysIncomeOfDay as $incomeKey=>$incomeItem) {{round($incomeItem['deposit_fee'], 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                {
                    name:'溢价收益',
                    type:'bar',
                    stack: '广告',
                    data:[@foreach($otcSysIncomeOfDay as $incomeKey=>$incomeItem) {{round($incomeItem['quick_income'] ?? 0, 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                {
                    name:'总收益',
                    type:'bar',
                    data:[@foreach($otcSysIncomeOfDay as $incomeKey=>$incomeItem) {{round($incomeItem['total'], 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                }
            ]
        };
        otcSysIncomeOfDay.setOption(otcSysIncomeOfDayOption);
    </script>

    {{--OTC平台各商户所贡献总收益统计 - 每天 - 默认USDT--}}
    <script>
        var incomeByMerchantOfDay = echarts.init(document.getElementById('incomeByMerchantOfDay'));
        var incomeByMerchantOfDayOption = {
            title : {
                text: 'OTC 平台商户贡献收益',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            toolbox: {
                show : true,
                feature : {
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            legend: {
                //data:['订单手续费','充值手续费','溢价收益','总收益']
                data:[@foreach($incomeByMerchantOfDay['merchant'] as $merchant) '{{$merchant=='total'?'总收益':$merchant}}', @endforeach]
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    data : [@foreach($incomeByMerchantOfDay['data'] as $incomeKey=>$incomeItem) '{{date('n/d', strtotime($incomeKey))}}', @endforeach]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                @foreach($incomeByMerchantOfDay['merchant'] as $merchant)
                {
                    name:'{{$merchant=='total'?'总收益':$merchant}}',
                    type:'bar',
                    data:[@foreach($incomeByMerchantOfDay['data'] as $incomeKey=>$incomeItem) {{round($incomeItem[$merchant], 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                @endforeach
            ]
        };
        incomeByMerchantOfDay.setOption(incomeByMerchantOfDayOption);
    </script>

    {{--OTC平台各商户贡献总收益及出入金总额统计 - 默认USDT--}}
    <script>
        var incomeByMerchant = echarts.init(document.getElementById('incomeByMerchant'));
        var incomeByMerchantOption = {
            title : {
                text: 'OTC 平台商户贡献收益及出入金总额',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            legend: {
                data: ['交易手续费', '溢价总收益','入金总数额','出金总数额']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'value'
                }
            ],
            yAxis : [
                {
                    type : 'category',
                    axisTick : {show: false},
                    // data: ['商户1', ... '商户n']
                    data:[@foreach($incomeByMerchant['merchant'] as $merchant) '{{$merchant}}', @endforeach]
                }
            ],
            series : [
                {
                    name:'交易手续费',
                    type:'bar',
                    stack:'利润',
                    label: {
                        normal: {
                            show: true,
                            position: 'right'
                        }
                    },
                    data:[@foreach($incomeByMerchant['data']['fee'] as $incomeKey=>$incomeItem) {{round($incomeItem, 2)}}, @endforeach]
                },
                {
                    name:'溢价总收益',
                    type:'bar',
                    stack:'利润',
                    label: {
                        normal: {
                            show: true,
                            position: 'left'
                        }
                    },
                    data:[@foreach($incomeByMerchant['data']['income_sys'] as $incomeKey=>$incomeItem) {{round($incomeItem, 2)}}, @endforeach]
                },
                {
                    name:'入金总数额',
                    type:'bar',
                    stack:'交易总量',
                    label: {
                        normal: {
                            show: true,
                            position: 'insideRight'
                        }
                    },
                    data:[@foreach($incomeByMerchant['data']['field_amount_in'] as $incomeKey=>$incomeItem) {{round($incomeItem, 2)}}, @endforeach]
                },
                {
                    name:'出金总数额',
                    type:'bar',
                    stack:'交易总量',
                    label: {
                        normal: {
                            show: true,
                            position: 'insideRight'
                        }
                    },
                    data:[@foreach($incomeByMerchant['data']['field_amount_out'] as $incomeKey=>$incomeItem) {{round($incomeItem, 2)}}, @endforeach]
                }
            ]
        };
        incomeByMerchant.setOption(incomeByMerchantOption);
    </script>

    {{--OTC 各商户入金总额统计 - 每天 默认USDT--}}
    <script>
        var inByMerchantOfDay = echarts.init(document.getElementById('inByMerchantOfDay'));
        var inByMerchantOfDayOption = {
            title : {
                text: 'OTC 平台商户入金',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            toolbox: {
                show : true,
                feature : {
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            legend: {
                //data:['商户1入金','商户2入金',....,入金总额']
                data:[@foreach($inByMerchantOfDay['merchant'] as $merchant) '{{$merchant=='today_buy_amount'?'入金总额':$merchant}}', @endforeach]
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    data : [@foreach($inByMerchantOfDay['data'] as $incomeKey=>$incomeItem) '{{date('n/d', strtotime($incomeKey))}}', @endforeach]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                    @foreach($inByMerchantOfDay['merchant'] as $merchant)
                {
                    name:'{{$merchant=='today_buy_amount'?'入金总额':$merchant}}',
                    type:'bar',
                    data:[@foreach($inByMerchantOfDay['data'] as $incomeKey=>$incomeItem) {{
                        round($merchant=='today_buy_amount' ? $incomeItem[$merchant] : $incomeItem[$merchant]['otc_buy_amount'], 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                @endforeach
            ]
        };
        inByMerchantOfDay.setOption(inByMerchantOfDayOption);
    </script>

    {{--OTC 各商户入金贡献手续费总额统计 - 每天 默认USDT--}}
    <script>
        var feeByMerchantOfDay = echarts.init(document.getElementById('feeByMerchantOfDay'));
        var feeByMerchantOfDayOption = {
            title : {
                text: 'OTC 平台商户入金手续费',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis',
                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                }
            },
            toolbox: {
                show : true,
                feature : {
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            legend: {
                //data:['商户1入金手续费','商户2入金手续覅',....,总手续费']
                data:[@foreach($feeByMerchantOfDay['merchant'] as $merchant) '{{$merchant=='today_fee'?'总手续费':$merchant}}', @endforeach]
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis : [
                {
                    type : 'category',
                    data : [@foreach($feeByMerchantOfDay['data'] as $incomeKey=>$incomeItem) '{{date('n/d', strtotime($incomeKey))}}', @endforeach]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                    @foreach($feeByMerchantOfDay['merchant'] as $merchant)
                {
                    name:'{{$merchant=='today_fee'?'总手续费':$merchant}}',
                    type:'bar',
                    data:[@foreach($feeByMerchantOfDay['data'] as $incomeKey=>$incomeItem) {{
                        round($merchant=='today_fee' ? $incomeItem[$merchant] : $incomeItem[$merchant]['otc_buy_fee'], 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                @endforeach
            ]
        };
        feeByMerchantOfDay.setOption(feeByMerchantOfDayOption);
    </script>

    {{--OTC订单卖出及手续费统计 - 默认USDT--}}
    <script>
        var otcSellOfDay = echarts.init(document.getElementById('otcSellOfDay'));
        var otcSellOfDayOption = {
            title : {
                text: 'OTC 订单卖出及手续费',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis'
            },
            legend: {
                data:['卖出量','手续费']
            },
            toolbox: {
                show : true,
                feature : {
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type : 'category',
                    data : [@foreach($otcSellOfDay as $sellKey=>$sellItem) '{{date('n/d',strtotime($sellItem->time))}}', @endforeach]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'卖出量',
                    type:'bar',
                    data:[@foreach($otcSellOfDay as $key=>$item) {{round($item->amount, 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                {
                    name:'手续费',
                    type:'bar',
                    data:[@foreach($otcSellOfDay as $key=>$item) {{round($item->fee, 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                }
            ]
        };
        otcSellOfDay.setOption(otcSellOfDayOption);
    </script>
    @endif

    {{--OTC充值及手续费统计 - 默认USDT--}}
    <script>
        var transFeeDepositOfDay = echarts.init(document.getElementById('transFeeDepositOfDay'));
        var transFeeDepositOfDayOption = {
            title : {
                text: 'OTC 充值及手续费',
                subtext: 'USDT',
            },
            tooltip : {
                trigger: 'axis'
            },
            legend: {
                data:['充值量','手续费']
            },
            toolbox: {
                show : true,
                feature : {
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type : 'category',
                    data : [@foreach($transFeeDepositOfDay as $sellKey=>$sellItem) '{{date('n/d',strtotime($sellItem->time))}}', @endforeach]
                }
            ],
            yAxis : [
                {
                    type : 'value'
                }
            ],
            series : [
                {
                    name:'充值量',
                    type:'bar',
                    data:[@foreach($transFeeDepositOfDay as $key=>$item) {{round($item->amount, 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                },
                {
                    name:'手续费',
                    type:'bar',
                    data:[@foreach($transFeeDepositOfDay as $key=>$item) {{round($item->fee, 2)}}, @endforeach],
                    markPoint : {
                        data : [
                            {type : 'max', name: '最大值'},
                            {type : 'min', name: '最小值'}
                        ]
                    },
                    markLine : {
                        data : [
                            {type : 'average', name: '平均值'}
                        ]
                    }
                }
            ]
        };
        transFeeDepositOfDay.setOption(transFeeDepositOfDayOption);
    </script>

    {{--币种信息统计--}}
    <script>
        var currency = echarts.init(document.getElementById('currency'));
        var currencyOption = {
                title: {
                    text: '币种信息统计',
                    subtext: '系统币种信息'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow'
                    }
                },
                legend: {
                    data: ['发行数量', '流通数量']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    boundaryGap: [0, 0.01]
                },
                yAxis: {
                    type: 'category',
                    data: [
                        @foreach($currency['en'] as $key => $v)
                            '{{$v}}',
                        @endforeach
                        '系统币种']
                },
                series: [
                    {
                        name: '发行数量',
                        type: 'bar',
                        data: [
                            @foreach($currency['amount'] as $key => $v)
                                {{$v}},
                            @endforeach
                            {{ $currency['amountTotal']}}
                        ]
                    },
                    {
                        name: '流通数量',
                        type: 'bar',
                        data: [
                            @foreach($currency['circulation'] as $key => $v)
                            {{$v}},
                            @endforeach
                            {{ $currency['circulationTotal']}}
                        ]
                    }
                ]
        };
        currency.setOption(currencyOption);
    </script>

    @if(env('APP_TC_MODULE'))
        {{--充值订单数量及额度统计-按状态--}}
        <script>
            var depositOrderStatus = echarts.init(document.getElementById('depositOrderStatus'));
            var depositOrderStatusOption = {
                title: {
                    text: 'Today 充值订单金额',
                    subtext: '按处理状态分类'
                },
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    data: ['订单状态','充值金额']
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis:  {
                    type: 'value'
                },
                yAxis: {
                    type: 'category',
                    data: [
                        @foreach($depositOrderStatus['orderStatus'] as $key => $v)
                            '{{$v}}',
                        @endforeach
                    ]
                },
                series: [
                    {
                        name: '订单数量',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: false,
                                position: 'insideRight'
                            }
                        },
                        data: [
                            @for($i = 6; $i > 0; $i--)
                                @if(isset($depositOrderStatus['order'][$i]))
                                    {{$depositOrderStatus['order'][$i]->orderNum}},
                                @else
                                    0,
                                @endif
                            @endfor
                        ]
                    },
                    {
                        name: '充值金额',
                        type: 'bar',
                        stack: '总量',
                        label: {
                            normal: {
                                show: false,
                                position: 'insideRight'
                            }
                        },
                        data: [
                            @for($i = 6; $i > 0; $i--)
                                @if(isset($depositOrderStatus['order'][$i]))
                                    {{$depositOrderStatus['order'][$i]->deposit_amount}},
                                @else
                                    0,
                                @endif
                            @endfor
                        ]
                    },
                ]
            };
            depositOrderStatus.setOption(depositOrderStatusOption);
        </script>

        {{--提币订单数量及额度统计-按状态--}}
        <script>
            var withdrawOrderStatus = echarts.init(document.getElementById('withdrawOrderStatus'));
            var withdrawOrderStatusOption = {
                title: {
                    text: 'Today 提币订单金额',
                    subtext: '按处理状态分类'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    show : true,
                    showTitle: false,
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['提币金额','订单数量']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: [
                            @foreach($withdrawOrderStatus['orderStatus'] as $key => $v)
                                '{{ $v }}',
                            @endforeach
                        ],
                        axisPointer: {
                            type: 'shadow'
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '金额',
                        min: 0,
                        max: {{ $withdrawOrderStatus['maxAmount'] }},
                        interval: {{ $withdrawOrderStatus['amountInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    },
                    {
                        type: 'value',
                        name: '订单数',
                        min: 0,
                        max: {{ $withdrawOrderStatus['maxOrder'] }},
                        interval: {{ $withdrawOrderStatus['orderInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    }
                ],
                series: [
                    {
                        name:'提币金额',
                        type:'bar',
                        data:[
                            @foreach($withdrawOrderStatus['order'] as $key => $v)
                                {{$v->amount}},
                            @endforeach
                        ]
                    },

                    {
                        name:'订单数量',
                        type:'line',
                        yAxisIndex: 1,
                        data:[
                            @foreach($withdrawOrderStatus['order'] as $key => $v)
                                {{$v->orderNum}},
                            @endforeach
                        ]
                    }
                ]
            };
            withdrawOrderStatus.setOption(withdrawOrderStatusOption);
        </script>

        {{--委托订单数量--按状态--}}
        <script>
            var exchangeOrderByStatus = echarts.init(document.getElementById('exchangeOrderByStatus'));
            var exchangeOrderByStatusOption = {
                title : {
                    text: 'Today 委托订单数量',
                    subtext: '按处理状态分类',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: [
                        @foreach($exchangeOrderByStatus['status'] as $key => $v)
                            '{{ $v }}',
                        @endforeach
                    ]
                },
                series : [
                    {
                        name: '委托订单',
                        type: 'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        data:[
                            @foreach($exchangeOrderByStatus['status'] as $key => $v)
                                @if(isset($exchangeOrderByStatus['order'][$key]))
                                    {value:{{$exchangeOrderByStatus['order'][$key]->statusNum}}, name:'{{$exchangeOrderByStatus['order'][$key]->statusName}}' },
                                @else
                                    {value:0, name:'{{ $v }}'},
                                @endif
                            @endforeach
                        ],
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]

            };
            exchangeOrderByStatus.setOption(exchangeOrderByStatusOption);
        </script>

        {{--委托订单成交数量及金额--按类型--}}
        <script>
            var exchangeOrderByType = echarts.init(document.getElementById('exchangeOrderByType'));
            var exchangeOrderByTypeOption = {
                title: {
                    text: 'Today 成交订单金额',
                    subtext: '按类型分类'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    show : true,
                    showTitle: false,
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['成交总额','已成交数量']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: [
                            @foreach($exchangeOrderByType['type'] as $key => $v)
                                '{{ $v }}',
                            @endforeach
                        ],
                        axisPointer: {
                            type: 'shadow'
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '成交总额',
                        min: 0,
                        max: {{ $exchangeOrderByType['maxCashAmount'] }},
                        interval: {{ $exchangeOrderByType['cashAmountInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    },
                    {
                        type: 'value',
                        name: '已成交数量',
                        min: 0,
                        max: {{ $exchangeOrderByType['maxAmount'] }},
                        interval: {{ $exchangeOrderByType['amountInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    }
                ],
                series: [
                    {
                        name:'成交总额',
                        type:'bar',
                        data:[
                            @foreach($exchangeOrderByType['order'] as $key => $v)
                            {{$v->cash_amount}},
                            @endforeach
                        ]
                    },

                    {
                        name:'已成交数量',
                        type:'line',
                        yAxisIndex: 1,
                        data:[
                            @foreach($exchangeOrderByType['order'] as $key => $v)
                            {{$v->amount}},
                            @endforeach
                        ]
                    }
                ]
            };
            exchangeOrderByType.setOption(exchangeOrderByTypeOption);
        </script>

        {{--委托订单成交数量及价格--按类型--}}
        <script>
            var exchangeOrderLog = echarts.init(document.getElementById('exchangeOrderLog'));
            var exchangeOrderLogOption = {
                title: {
                    text: 'Today 成交订单价格',
                    subtext: '按类型分类'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    show : true,
                    showTitle: false,
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['成交价格','成交数量']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: [
                            @foreach($exchangeOrderLog['type'] as $key => $v)
                                '{{ $v }}',
                            @endforeach
                        ],
                        axisPointer: {
                            type: 'shadow'
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '成交价格',
                        min: 0,
                        max: {{ $exchangeOrderLog['maxPrice'] }},
                        interval: {{ $exchangeOrderLog['priceInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    },
                    {
                        type: 'value',
                        name: '成交数量',
                        min: 0,
                        max: {{ $exchangeOrderLog['maxAmount'] }},
                        interval: {{ $exchangeOrderLog['amountInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    }
                ],
                series: [
                    {
                        name:'成交价格',
                        type:'bar',
                        data:[
                            @foreach($exchangeOrderLog['order'] as $key => $v)
                            {{$v->price}},
                            @endforeach
                        ]
                    },

                    {
                        name:'成交数量',
                        type:'line',
                        yAxisIndex: 1,
                        data:[
                            @foreach($exchangeOrderLog['order'] as $key => $v)
                            {{$v->amount}},
                            @endforeach
                        ]
                    }
                ]
            };
            exchangeOrderLog.setOption(exchangeOrderLogOption);
        </script>
    @endif

    @if(env('APP_OTC_MODULE'))
        {{----OTC 统计区域----}}
        {{--OTC 订单成交数量及价格--按类型--}}
        <script>
            var otcOrder = echarts.init(document.getElementById('otcOrder'));
            var otcOrderOption = {
                title: {
                    text: 'Today OTC订单价格',
                    subtext: '按状态分类'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    show : true,
                    showTitle: false,
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['交易均价','交易数量']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: [
                            @foreach($otcOrder['status'] as $key => $v)
                                '{{ $v }}',
                            @endforeach
                        ],
                        axisPointer: {
                            type: 'shadow'
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '交易均价',
                        min: 0,
                        max: {{ $otcOrder['maxPrice'] }},
                        interval: {{ $otcOrder['priceInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    },
                    {
                        type: 'value',
                        name: '交易数量',
                        min: 0,
                        max: {{ $otcOrder['maxAmount'] }},
                        interval: {{ $otcOrder['amountInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    }
                ],
                series: [
                    {
                        name:'交易均价',
                        type:'bar',
                        data:[
                            @foreach($otcOrder['order'] as $key => $v)
                            {{$v->price}},
                            @endforeach
                        ]
                    },

                    {
                        name:'交易数量',
                        type:'line',
                        yAxisIndex: 1,
                        data:[
                            @foreach($otcOrder['order'] as $key => $v)
                            {{$v->amount}},
                            @endforeach
                        ]
                    }
                ]
            };
            otcOrder.setOption(otcOrderOption);
        </script>

        {{--OTC 提币订单数量及额度统计-按状态--}}
        <script>
            var otcWithdrawOrderStatus = echarts.init(document.getElementById('otcWithdrawOrderStatus'));
            var otcWithdrawOrderStatusOption = {
                title: {
                    text: 'Today OTC提币订单金额',
                    subtext: '按处理状态分类'
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    show : true,
                    showTitle: false,
                    feature: {
                        dataView: {show: true, readOnly: false},
                        magicType: {show: true, type: ['line', 'bar']},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                legend: {
                    data:['提币金额','订单数量']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: [
                            @foreach($otcWithdrawOrderStatus['orderStatus'] as $key => $v)
                                '{{ $v }}',
                            @endforeach
                        ],
                        axisPointer: {
                            type: 'shadow'
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '金额',
                        min: 0,
                        max: {{ $otcWithdrawOrderStatus['maxAmount'] }},
                        interval: {{ $otcWithdrawOrderStatus['amountInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    },
                    {
                        type: 'value',
                        name: '订单数',
                        min: 0,
                        max: {{ $otcWithdrawOrderStatus['maxOrder'] }},
                        interval: {{ $otcWithdrawOrderStatus['orderInterval'] }},
                        axisLabel: {
                            formatter: '{value} '
                        }
                    }
                ],
                series: [
                    {
                        name:'提币金额',
                        type:'bar',
                        data:[
                            @foreach($otcWithdrawOrderStatus['order'] as $key => $v)
                            {{$v->amount}},
                            @endforeach
                        ]
                    },

                    {
                        name:'订单数量',
                        type:'line',
                        yAxisIndex: 1,
                        data:[
                            @foreach($otcWithdrawOrderStatus['order'] as $key => $v)
                            {{$v->orderNum}},
                            @endforeach
                        ]
                    }
                ]
            };
            otcWithdrawOrderStatus.setOption(otcWithdrawOrderStatusOption);
        </script>
    @endif
@endsection