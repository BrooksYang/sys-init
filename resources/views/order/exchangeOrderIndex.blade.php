@extends('entrance::layouts.default')

@section('css-part')
    @parent
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    {{-- Search --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('order/exchange') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索币种(交易对)或用户" name="search" value="{{ Request::get('search') ?? '' }}">
                            <a href="javascript:;" title="搜索币种(交易对)或用户信息">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                       {{-- <a href="{{ url('order/exchange/create') }}" title="">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        {{--筛选类别--}}
                        <div style="display: inline-block;position: relative">
                            <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按类别筛选订单">
                                <span class="box-btn"><i class="fontello-menu" title="按类别筛选订单"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ url('order/exchange') }}">全部
                                        {!!  !in_array(Request::get('filterType'),array_keys($type)) ? '&nbsp;<i class="fa fa-check txt-info"></i>' :''!!}
                                    </a>
                                </li>
                                @foreach($type as $key => $item)
                                    <li>
                                        <a href="{{ url('order/exchange') }}?filterType={{$key}}">{{$item['name']}}
                                            {!!  Request::get('filterType') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div style="display: inline-block;position: relative">
                            {{--筛选状态--}}
                            <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按状态筛选订单">
                                <span class="box-btn"><i class="fa fa-filter" title="按状态筛选订单"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ url('order/exchange') }}">全部
                                        {!! in_array( Request::get('filterStatus'),array_keys($status)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                    </a>
                                </li>
                                @foreach($status as $key => $item)
                                    <li>
                                        <a href="{{ url('order/exchange') }}?filterStatus={{$key}}">{{$item['name']}}
                                            {!!  Request::get('filterStatus') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统交易订单列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>订单号</th>
                                <th>用户手机</th>
                                <th>用户邮箱</th>
                                <th>交易对</th>
                                <th>挂单手续费</th>
                                <th>吃单手续费</th>
                                <th title="限价单数量、市价单金额或数量">数额</th>
                                <th>已成交数量</th>
                                <th>已成交总金额</th>
                                <th title="限价单价格">价格</th>
                                <th>成交均价</th>
                                <th>状态</th>
                                <th>类型</th>
                                <th>撤销时间</th>
                                <th title="最后成交时间">成交时间 &nbsp;&nbsp;<a href="{{ url('order/exchange')}}?orderC=desc">
                                            <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                        <a href="{{ url('order/exchange') }}?orderC=asc">
                                            <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                            {{--<th>操作</th>--}}
                            </tr>
                            @forelse($order as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($order->currentPage() - 1) * $order->perPage() }}</td>
                                    <td title="{{ $item->id }}">{{ str_limit($item->id, 15) }}</td>
                                    <td title="{{ $item->phone }}">{{ str_limit($item->phone, 15) }}</td>
                                    <td title="{{ $item->email }}">{{ str_limit($item->email, 12) }}</td>
                                    <td><span class="label label-info">{{ $item->symbol }}</span></td>
                                    <td>{{ $item->maker_fee.'%'}}</td>
                                    <td>{{ $item->taker_fee.'%'}}</td>
                                    <td>{{ number_format($item->amount,8,'.',',') }}</td>
                                    <td>{{ number_format(8198150123456.78,8,'.',',') }}</td>
                                    <td>{{ number_format($item->field_cash_amount,8,'.',',') }}</td>
                                    <td>{{ number_format($item->price,8,'.',',') }}</td>
                                    <td>{{ number_format($item->traded_average_price,8,'.',',') }}</td>
                                    <td>
                                        <span class="label label-{{ $status[$item->status]['class'] }}">{{ $status[$item->status]['name'] }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-{{ $type[$item->type]['class'] }}">{{ $type[$item->type]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->cancelled_at ?: '--'}}</td>
                                    <td>{{ $item->finished_at ?: '--'}}</td>
                                    {{--<td></td>--}}
                                </tr>
                            @empty
                                <tr><td colspan="15" class="text-center">
                                    <div class="noDataValue">
                                        暂无数据
                                    </div>
                                </td></tr>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-left" style="margin-top: 30px;">总计：{{ $order->total() }}</div>
                                <div class="pull-right">
                                    {{ $order->appends(Request::except('page'))->links() }}
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
    </script>
@endsection
