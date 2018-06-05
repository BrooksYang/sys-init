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
                        <form action="{{ url('otc/ad') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索币种或用户名或电话" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索币种或用户名或电话">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        {{--<a href="{{ url('otc/ad/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选广告">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选广告"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('otc/ad') }}">全部
                                    {!! in_array( Request::get('filterReceiptWay'),array_keys($status)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                </a>
                            </li>
                        @foreach($status as $key=>$item)
                            <li>
                                <a href="{{ url('otc/ad') }}?filterReceiptWay={{$key}}">{{$item['name']}}
                                {!!  Request::get('filterReceiptWay') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户 OTC 交易广告列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>类型</th>
                                <th>币种</th>
                                <th>单价</th>
                                <th>法币</th>
                                <th>数量</th>
                                <th>最低限额</th>
                                <th>最高限额</th>
                                <th>已成交数量</th>
                                <th>成单数</th>
                                <th>完成率</th>
                                <th>认证</th>
                                <th>收款方式</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('otc/ad')}}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :'' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('otc/ad') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                {{--<th>操作</th>--}}
                            </tr>
                            @forelse($otcAd as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($otcAd->currentPage() - 1) * $otcAd->perPage() }}</td>
                                    <td title="电话：{{$item->phone}}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td>
                                        <span class="label label-{{ $orderType[$item->type]['class'] }}">{{ $orderType[$item->type]['name'] }}</span>
                                    </td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_en_abbr,15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->price,8,'.',',') }}">{{ number_format($item->price,8,'.',',') }}</td>
                                    <td title="{{ $item->name }}">{{ $item->abbr }}</td>
                                    <td title="{{number_format($item->field_amount,8,'.',',') }}">{{ number_format($item->field_amount,8,'.',',') }}</td>
                                    <td title="{{number_format($item->floor,8,'.',',') }}">{{ number_format($item->floor,8,'.',',') }}</td>
                                    <td title="{{number_format($item->ceiling,8,'.',',') }}">{{ number_format($item->ceiling,8,'.',',') }}</td>
                                    <td title="{{number_format($item->field_amount,8,'.',',') }}">{{ number_format($item->field_amount,8,'.',',') }}</td>
                                    <td title="{{ $item->field_order_count }}">{{ $item->field_order_count }}</td>
                                    <td title="{{ $item->field_percentage }}%">{{ $item->field_percentage }}%</td>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">认证情况</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <span><b>手机认证：</b></span>{!! $item->need_phone_auth ?? $item->need_phone_auth ? '<span class="label label-info">需要</span>': '<span class="label label-default">不需要</span>' !!}
                                                        <p></p>
                                                        <span><b>高级认证：</b></span>{!! $item->need_advanced_auth ?? $item->need_phone_auth ? '<span class="label label-info">需要</span>': '<span class="label label-default">不需要</span>' !!}
                                                        <P></P>
                                                        <span><b>备注：</b></span>{!! $item->remark ?? '暂无说明' !!}
                                                        <P></P>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="label label-{{ $status[$item->status]['class'] }}">{{ $status[$item->status]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                   {{-- <td>
                                       --}}{{-- <a data-toggle="dropdown" class="dropdown-toggle" type="button">
                                            <span class="box-btn"><i class="fa fa-exchange" title="修改订单状态"></i></span>
                                        </a>
                                        <ul role="menu" class="dropdown-menu pull-right">
                                            @foreach($orderStatus as $flag=>$status)
                                                <li>
                                                    @if($item->status != $flag)
                                                    <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                            '{{ url("otc/ad/$item->id") }}','status',{{$flag}},
                                                            'OTC订单为<b><strong> {{$status['name']}} </strong></b> 状态',
                                                            '{{ csrf_token() }}', '{{$status['name']}}' );">
                                                    {{$status['name']}}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>--}}{{--
                                        --}}{{--<a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/ad/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>--}}{{--
                                    </td>--}}
                                </tr>
                            @empty
                                <tr><td colspan="16" class="text-center">
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
                                    {{ $otcAd->appends(Request::except('page'))->links() }}
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
