@extends('entrance::layouts.default')

@section('css-part')
    @parent
    <link href="{{ asset('/css/hbfont/hbfont.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .hbfont [class^="icon-"]:before,
        .hbfont [class*=" icon-"]:before{
            font-family: inherit;
        }
        .box-tools .fa,.box-title [class^="fontello-"]:before, [class*=" fontello-"]:before,.wrap-sidebar-content .fa,.wrap-sidebar-content [class^="icon-"]:before, [class*=" icon-"]:before{
            line-height: inherit;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('order/otc/quick/userDeposit') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索用户名或电话或邮箱" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索用户名或电话或邮箱">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        {{--<a href="{{ url('order/otc/quick/userDeposit/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <div style="display: inline-block;position: relative">
                            <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按状态筛选">
                                <span class="box-btn"><i class="fa fontello-menu" title="按状态筛选"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu">
                                <li>
                                    <a href="{{ url('order/otc/quick/userDeposit') }}">全部
                                        {!! in_array( Request::get('filterStatus'),array_keys($orderStatus)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                    </a>
                                </li>
                                @foreach($orderStatus as $key=>$item)
                                    <li>
                                        <a href="{{ url('order/otc/quick/userDeposit') }}?filterStatus={{$key}}">{{$item['name']}}
                                            {!!  Request::get('filterStatus') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按支付类型筛选">
                            <span class="box-btn"><i class="fa fa-filter" title="按支付类型筛选"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('order/otc/quick/userDeposit') }}">全部
                                    {!! in_array( Request::get('filterType'),array_keys($payType)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                </a>
                            </li>
                            @foreach($payType as $key=>$item)
                                <li>
                                    <a href="{{ url('order/otc/quick/userDeposit') }}?filterType={{$key}}">{{$item['name']}}
                                        {!!  Request::get('filterType') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户 OTC 快捷充值订单列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>订单号</th>
                                <th>金额(CNY)</th>
                                <th>金额(USDT)</th>
                                <th>汇率</th>
                                <th>交易流水号</th>
                                <th>支付类型</th>
                                <th>支付状态</th>
                                <th>完成支付时间</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('order/otc/quick/userDeposit')}}?orderC=desc{{'&'.str_replace(['&orderC=asc'],'',Request::getQueryString())}}">
                                        <i class="fa fa-sort-amount-desc" style="color:{{  strpos(Request::getQueryString(),'orderC=desc') !== false ?  '' : strpos(Request::getQueryString(),'orderC=asc') !== false ? 'gray' : '' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('order/otc/quick/userDeposit') }}?orderC=asc{{'&'.str_replace(['&orderC=desc'],'',Request::getQueryString())}}">
                                        <i class="fa fa-sort-amount-asc"  style="color:{{ strpos(Request::getQueryString(),'orderC=asc') !== false ?  '' : 'gray' }}" title="升序"></i></a>
                                </th>
                            </tr>
                            @forelse($otcQuickDeposits as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($otcQuickDeposits->currentPage() - 1) * $otcQuickDeposits->perPage() }}</td>
                                    <td title="{{$item->phone}}"><strong>{{ str_limit($item->username?$item->username:$item->phone,15) }}</strong></td>
                                    <td title="{{ $item->order_no }}">{{ $item->order_no }}</td>
                                    <td title="{{ number_format($item->amount,8)}}">{{ floatval($item->amount,8)}}</td>
                                    <td title="{{ number_format($item->usdt_amount,8)}}">{{ floatval($item->usdt_amount)}}</td>
                                    <td title="{{ number_format($item->rate,8)}}">{{ floatval($item->rate)}}</td>
                                    <td title="{{ $item->trade_no }}">{{$item->trade_no ?: '--'}}</td>
                                    <td class="hbfont" title="{{ $payType[$item->pay_type]['name'] }}"><i class="{{ $payType[$item->pay_type]['class'] }}"></i></td>
                                    <td>
                                        <span class="label label-{{ $orderStatus[$item->status]['class'] }}">{{ $orderStatus[$item->status]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->finished_at ?: '--' }}</td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">
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
                                    {{ $otcQuickDeposits->appends(Request::except('page'))->links() }}
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
