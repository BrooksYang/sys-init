@extends('entrance::layouts.default')

@section('css-part')
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        {{--<a href="{{ url('order/withdraw/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选订单">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选订单"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                        @foreach($orderStatus as $key=>$item)
                            <li>
                                <a href="{{ url('order/withdraw') }}?filter={{$key}}">{{$item['name']}}
                                {!!  Request::get('filter') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' :
                                $key ==1 && ! Request::get('filter') ? '&nbsp;<i class="fa fa-check txt-info"></i>' :'' !!}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户提币订单待受理列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>电话</th>
                                <th>币种</th>
                                <th>提币金额</th>
                                <th title="收币钱包ID">收币钱包</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            @forelse($userWithdrawOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userWithdrawOrder->currentPage() - 1) * $userWithdrawOrder->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->phone}}">{{ $item->phone }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->withdraw_amount,8,'.',',') }}">{{ number_format($item->withdraw_amount,8,'.',',') }}</td>
                                    <td title="{{ $item->crypto_wallet_title }}"><strong>{{ str_limit($item->crypto_wallet_title,15) }}</strong></td>
                                    <td>
                                        <span class="label label-{{ $orderStatus[$item->withdraw_order_status]['class'] }}">{{ $orderStatus[$item->withdraw_order_status]['name'] }}</span>
                                    </td>
                                    <td>
                                        <a data-toggle="dropdown" class="dropdown-toggle" type="button">
                                            <span class="box-btn"><i class="fa fa-exchange" title="修改订单状态"></i></span>
                                        </a>
                                        {{--TODO 订单下拉状态样式调整--}}
                                        <ul role="menu" class="dropdown-menu">
                                            @foreach($orderStatus as $flag=>$status)
                                                <li>
                                                    @if($item->withdraw_order_status != $flag)
                                                    <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                            '{{ url("order/withdraw/$item->id") }}?status={{$flag}}','withdraw_order_status',{{$flag}},
                                                            '提币订单为<b><strong> {{$status['name']}} </strong></b> 状态',
                                                            '{{ csrf_token() }}');">
                                                    {{$status['name']}}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("order/withdraw/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <span class="text-center">暂无数据</span>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $userWithdrawOrder->appends(Request::except('page'))->links() }}
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
