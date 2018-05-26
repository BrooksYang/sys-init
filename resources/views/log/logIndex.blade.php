@extends('entrance::layouts.default')

@section('css-import')
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/timepicker/bootstrap-timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/datepicker/datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/datepicker/clockface.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/entrance/css/entypo-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/entrance/css/entypo.css') }}">
@endsection

@section('css-part')
    @parent
    <style>
        .logList [class^="fontello-"]:before, [class*=" fontello-"]:before{
            font-size: 100%;
            line-height: inherit;
        }
        .logList .fa{
            line-height: inherit;
        }
        .modal button{width: auto}
        .box-tools .modal input{
            width: 200px!important;
        }
        .logdate{
            display: table-cell!important;
            border-top-right-radius:0!important;
            padding: 0 5px!important;
            border-bottom-right-radius: 0!important;
        }
    </style>
@endsection

@section('content')
    <div class="row logList">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('issuer/currencyTypeMg') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索模块名称或用户" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索模块名称或用户">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        {{--筛选时间--}}
                        <div style="display: inline-block;position: relative">
                            <!-- Button trigger modal -->
                            <a href="javascript:;"  class="" type="button" data-toggle="modal" data-target="#exampleModalLong">
                                <span class="box-btn"><i class="fontello-calendar" title="按时间筛选日志"></i></span>
                            </a>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">选择日志时间</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group {{ $errors->has('currency_issue_date') ? 'has-error' : '' }}">
                                                        <div class="col-lg-12">
                                                            <label>开始时间</label>
                                                            <div id="datetimepicker1" class="input-group date">
                                                                <input class="form-control" size="16" type="text" value="{{ Request::get('filterStartAt') ?? '' }}"
                                                                       placeholder="选择日志开始时间" data-format="yyyy-MM-dd hh:mm:ss" id="startAt">
                                                                <span class="input-group-addon add-on logdate">
                                                                <i style="font-style:normal;" data-time-icon="entypo-clock" data-date-icon="entypo-calendar"> </i></span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>截止时间</label>
                                                    <div id="datetimepicker2" class="input-group date">
                                                        <input class="form-control" size="16" type="text" value="{{ Request::get('filterEndAt') ?? '' }}"
                                                               placeholder="选择日志截止时间" data-format="yyyy-MM-dd hh:mm:ss" id="EndAt">
                                                        <span class="input-group-addon add-on logdate">
                                                        <i style="font-style:normal;" data-time-icon="entypo-clock" data-date-icon="entypo-calendar"> </i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="filterAt()">确定</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{--筛选级别--}}
                        <div style="display: inline-block;position: relative">
                            <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按级别筛选日志">
                                <span class="box-btn"><i class="fontello-menu" title="按级别筛选日志"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ url('backend/log') }}">全部
                                        {!!  !in_array(Request::get('filterLevel'),array_keys($level)) ? '&nbsp;<i class="fa fa-check txt-info"></i>' :''!!}
                                    </a>
                                </li>
                                @foreach($level as $key => $item)
                                    <li>
                                        <a href="{{ url('backend/log') }}?filterLevel={{$key}}">{{$item['name']}}
                                            {!!  Request::get('filterLevel') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        {{--筛选请求方法--}}
                        <div style="display: inline-block;position: relative">
                            <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按请求方法筛选日志">
                                <span class="box-btn"><i class="fa fa-filter" title="按请求方法筛选日志"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ url('backend/log') }}">全部
                                        {!! in_array( Request::get('filterMethod'),array_keys($method)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                    </a>
                                </li>
                                @foreach($method as $key => $item)
                                    <li>
                                        <a href="{{ url('backend/log') }}?filterMethod={{$key}}">{{$item['name']}}
                                            {!!  Request::get('filterMethod') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统管理端日志数据列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>账号</th>
                                <th>IP</th>
                                <th>模块</th>
                                <th>操作描述</th>
                                <th>级别</th>
                                <th>请求方法</th>
                                <th>用户代理</th>
                                <th>时间</th>
                                <th>详情</th>
                            </tr>
                            @forelse($log as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($log->currentPage() - 1) * $log->perPage() }}</td>
                                    <td title="{{ $item->email }}"><strong>{{ str_limit($item->email,25) }}</strong></td>
                                    <td><strong>{{$item->ip }}</strong></td>
                                    <td><strong>{{ $item->app }}</strong></td>
                                    <td title="{{ $item->message }}"><strong>{{ str_limit($item->message, 25) }}</strong></td>
                                    <td><span class="label label-{{ $level[$item->level]['class'] }}">{{ $level[$item->level]['name'] }}</span></td>
                                    <td><span class="label label-{{ $method[$item->method]['class'] }}">{{ $method[$item->method]['name'] }}</span></td>
                                    <td>
                                     <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">用户代理</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body {{ $item->agent ? '' : 'text_c' }}">
                                                        {{$item->agent ?: '暂无数据'}}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->datetime ?: '--'}}</td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLongLog{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLongLog{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitleLog{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitleLog{{$key}}">日志数据&nbsp;{{ $item->email }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <span><b>用户ID：</b></span>{{ $item->uid }}
                                                        <p></p>
                                                        <span><b>账号：</b></span>{{ $item->email }}
                                                        <p></p>
                                                        <span><b>IP：</b></span>{{ $item->ip ?: '--' }}
                                                        <p></p>
                                                        <span><b>模块：</b></span>{{ $item->app ?: '--' }}
                                                        <p></p>
                                                        <span><b>描述：</b></span>{{ $item->message ?: '--' }}
                                                        <p></p>
                                                        <span><b>级别：</b></span><span class="label label-{{ $level[$item->level]['class'] }}">{{ $level[$item->level]['name'] }}</span>
                                                        <p></p>
                                                        <span><b>日志时间：</b></span>{{ $item->datetime ?? $item->created_at }}
                                                        <p></p>
                                                        <span><b>类型：</b></span>{{ $item->type ?: '--' }}
                                                        <p></p>
                                                        <span><b>路由：</b></span>{{ $item->route ?: '--' }}
                                                        <p></p>
                                                        <span><b>请求方法：</b></span>{{ $item->method ?: '--' }}
                                                        <p></p>
                                                        <span><b>参数：</b></span><?php
                                                            if($item->parameter) {var_dump(json_decode($item->parameter,true));}else{ echo '--';} ?>
                                                        <p></p>
                                                        <span><b>Referer：</b></span>{{ $item->referer ?: '--' }}
                                                        <p></p>
                                                        <span><b>Session：</b></span>{{ $item->session ?: '--' }}
                                                        <p></p>
                                                        {!! $item->context ? '<span><b>Context：</b></span>'.$item->context .'<p></p>': '' !!}
                                                        {!! $item->extra ? '<span><b>备注：</b></span>'.$item->extra .'<p></p>': '' !!}
                                                        <span><b>用户代理：</b></span>{{ $item->agent }}
                                                        <p></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{--<td>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("issuer/currencyTypeMg/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>--}}
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center">
                                        <div class="noDataValue">
                                            暂无数据
                                        </div>
                                    </td></tr>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $log->appends(Request::except('page'))->links() }}
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
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/timepicker/bootstrap-timepicker.js') }}'></script>
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/datepicker/bootstrap-datepicker.js') }}'></script>
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/datepicker/clockface.js') }}'></script>
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/datepicker/bootstrap-datetimepicker.js') }}'></script>
    <script>
        $(function () {
            //日期时间插件
            $('#datetimepicker1').datetimepicker({
                language: 'zh'
            });
            $('#datetimepicker2').datetimepicker({
                language: 'zh'
            });
        });
        
        function filterAt() {
            window.location.href='{{ url('backend/log')}}'+'/?filterStartAt='+$('#startAt').val()+'&filterEndAt='+$('#EndAt').val();
        }
    </script>
@endsection
