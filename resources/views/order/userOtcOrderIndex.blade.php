@extends('entrance::layouts.default')

@section('css-part')
    @parent
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('order/otc') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索币种或用户名或电话" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索币种或用户名或电话">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        {{--<a href="{{ url('order/otc/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选订单">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选订单"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('order/otc') }}">全部
                                    {!! in_array( Request::get('filterStatus'),array_keys($orderStatus)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                </a>
                            </li>
                        @foreach($orderStatus as $key=>$item)
                            <li>
                                <a href="{{ url('order/otc') }}?filterStatus={{$key}}">{{$item['name']}}
                                {!!  Request::get('filterStatus') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户 OTC 交易订单列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                {{--<th>广告用户</th>--}}
                                <th>类型</th>
                                <th>币种</th>
                                <th>单价</th>
                                <th>法币</th>
                                <th>数量</th>
                                <th>总价</th>
                                <th>状态</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('order/otc')}}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :'' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('order/otc') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($userOtcOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcOrder->currentPage() - 1) * $userOtcOrder->perPage() }}</td>
                                    <td title="电话：{{$item->phone}}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                   {{-- <td title="{{ $item->from_username }} 电话：{{$item->from_user_phone}}"><strong>{{ str_limit($item->from_username,15) }}</strong></td>--}}
                                    <td>
                                        <span class="label label-{{ $orderType[$item->type]['class'] }}">{{ $orderType[$item->type]['name'] }}</span>
                                    </td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->price,8,'.',',') }}">{{ number_format($item->price,8,'.',',') }}</td>
                                    <td title="{{ $item->name }}">{{ $item->abbr }}</td>
                                    <td title="{{number_format($item->field_amount,8,'.',',') }}">{{ number_format($item->field_amount,8,'.',',') }}</td>
                                    <td title="{{number_format($item->cash_amount,8,'.',',') }}">{{ number_format($item->cash_amount,8,'.',',') }}</td>
                                    <td>
                                        <span class="label label-{{ $orderStatus[$item->status]['class'] }}">{{ $orderStatus[$item->status]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                    <td>
                                       {{-- <a data-toggle="dropdown" class="dropdown-toggle" type="button">
                                            <span class="box-btn"><i class="fa fa-exchange" title="修改订单状态"></i></span>
                                        </a>
                                        <ul role="menu" class="dropdown-menu pull-right">
                                            @foreach($orderStatus as $flag=>$status)
                                                <li>
                                                    @if($item->status != $flag)
                                                    <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                            '{{ url("order/otc/$item->id") }}','status',{{$flag}},
                                                            'OTC订单为<b><strong> {{$status['name']}} </strong></b> 状态',
                                                            '{{ csrf_token() }}', '{{$status['name']}}' );">
                                                    {{$status['name']}}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>--}}
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("order/otc/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
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
                                    {{ $userOtcOrder->appends(Request::except('page'))->links() }}
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
