@extends('entrance::layouts.default')

@section('content')
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
        <div class="col-lg-6">
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
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
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

                    <hr>
                    <div class="row">
                        <div class="col-md-10">
                            <!-- 币种信息统计 -->
                            <div id="currency" style="width: 100%;height:600px;"></div>
                        </div>
                    </div>

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
                            <!-- 委托订单成家数量及价格统计--按状态 -->
                            <div id="exchangeOrderLog" style="width: 100%;height:600px;"></div>
                        </div>
                    </div>

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
                    name: '成家总额',
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
                    name: '成家价格',
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
@endsection