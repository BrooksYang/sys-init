@extends('entrance::layouts.default')

@section('css-part')
    <style></style>
@endsection

@section('content')
    <!-- START 客服工单统计区域 -->
    <div class="row">
        <div class="col-lg-6">
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="news-widget">
                        <h2>
                            <span class="bg-green">Today</span>
                        </h2>
                        <i class="fontello-user-1"></i>
                        <h4 class="text-green">{{ number_format($myTicket,0,'',',') }}</h4>
                        <h5>我的工单</h5>
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
                        <i class="fontello-user-1"></i>
                        <h4 class="text-red">{{ number_format($myTicketByWaitingFor,0,'',',') }}</h4>
                        <h5>我的待处理工单</h5>
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
                            <span class="bg-aqua">Today</span>
                        </h2>
                        <i class="fontello-user-1"></i>
                        <h4 class="text-aqua">{{ number_format($sysTicketByNotAssign,0,'',',') }}</h4>
                        <h5>系统未分配工单</h5>
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
                            <span class="bg-blue">Today</span>
                        </h2>
                        <i class="fontello-user-1"></i>
                        <h4 class="text-blue">{{ number_format($sysTicketByWaitingFor,0,'',',') }}</h4>
                        <h5>系统待处理的工单</h5>
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
                    <hr>
                    <div class="row">
                        <div class="col-md-10">
                            <!-- OTC 客服我的工单统计--按状态 -->
                            <div id="myTicketByStatus" style="width: 100%;height:600px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END 客服工单统计区域 -->

@endsection

@section('js-part')
    <script src="{{ asset('/assets/Echarts/echarts.min.js') }}"></script>
    {{--OTC 客服我的工单统计-按状态--}}
    <script>
        var myTicketByStatus = echarts.init(document.getElementById('myTicketByStatus'));
        var myTicketByStatusOption = {
            title : {
                text: '我的工单统计',
                subtext: '按工单状态分类',
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
                    @foreach($myTicketByStatus['status'] as $key => $v)
                        '{{ $v }}',
                    @endforeach
                ]
            },
            series : [
                {
                    name: '我的工单',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:[
                            @foreach($myTicketByStatus['status'] as $key => $v)
                            @if(isset($myTicketByStatus['order'][$key]))
                        {value:{{$myTicketByStatus['order'][$key]->statusNum}}, name:'{{$myTicketByStatus['order'][$key]->statusName}}' },
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
        myTicketByStatus.setOption(myTicketByStatusOption);
    </script>
@endsection