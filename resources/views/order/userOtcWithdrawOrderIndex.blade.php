@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('order/otc/withdraw') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索币种或用户名或电话" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索币种或用户名或电话">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        {{--<a href="{{ url('order/otc/withdraw/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选订单">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选订单"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                        @foreach($orderStatus as $key=>$item)
                            <li>
                                <a href="{{ url('order/otc/withdraw') }}?filter={{$key}}">{{$item['name']}}
                                {!!  Request::get('filter') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' :
                                $key ==0 && ! Request::get('filter') ? '&nbsp;<i class="fa fa-check txt-info"></i>' :'' !!}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 用户提币订单待受理列表</span>
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
                                <th title="收币钱包地址">收币地址</th>
                                <th>状态</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('order/otc/withdraw')}}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :'' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('order/otc/withdraw') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($userOtcWithdrawOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcWithdrawOrder->currentPage() - 1) * $userOtcWithdrawOrder->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->phone}}">{{ $item->phone }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->amount,8,'.',',') }}">{{ number_format($item->amount,8,'.',',') }}</td>
                                    <td title="{{ $item->crypto_wallet_title }}"><strong>{{ $item->crypto_wallet_address }}</strong></td>
                                    <td>
                                        <span class="label label-{{ $orderStatus[$item->status]['class'] }}">{{ $orderStatus[$item->status]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                    <td>
                                        @if(in_array($item->status, [\App\Models\OTC\OtcWithdraw::OTC_PENDING,\App\Models\OTC\OtcWithdraw::OTC_FAILED]) )
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("order/otc/withdraw/$item->id") }}','status',3,
                                                    ' OTC 提币订单为<b><strong> 已发币 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '已发币' );" title="已发币"> <i class="fontello-ok"></i> </a>
                                        @elseif(in_array($item->status, [\App\Models\OTC\OtcWithdraw::OTC_WAITING] ))
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("order/otc/withdraw/$item->id") }}','status',2,
                                                    ' OTC 提币订单为<b><strong> 处理中 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '处理中' );" title="处理中"> <i class=" fontello-loop"></i> </a>
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("order/otc/withdraw/$item->id") }}','status',3,
                                                    ' OTC 提币订单为<b><strong> 已发币 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '已发币' );" title="已发币"> <i class="fontello-ok"></i>
                                            </a><a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("order/otc/withdraw/$item->id") }}','status',4,
                                                    ' OTC 提币订单为<b><strong> 失败 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '失败' );" title="失败"> <i class="fontello-reply"></i> </a>
                                        @endif
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("order/otc/withdraw/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">
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
                                    {{ $userOtcWithdrawOrder->appends(Request::except('page'))->links() }}
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
