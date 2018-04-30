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
                        {{--<a href="{{ url('order/userDeposit/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选订单">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选订单"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                        @foreach($orderStatus as $key=>$item)
                            <li>
                                <a href="{{ url('order/userDeposit') }}?filter={{$key}}">{{$item['name']}}
                                {!!  Request::get('filter') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' :
                                $key ==1 && ! Request::get('filter') ? '&nbsp;<i class="fa fa-check txt-info"></i>' :'' !!}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户充值订单列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名称</th>
                                <th>电话</th>
                                <th>币种</th>
                                <th>充值余额</th>
                                <th>交易号</th>
                                <th title="运营方数字钱包">钱包名称</th>
                                <th>凭证</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            @forelse($userDepositOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userDepositOrder->currentPage() - 1) * $userDepositOrder->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->phone}}">{{ $item->phone }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{$item->deposit_amount}}">{{ $item->deposit_amount }}</td>
                                    <td title="{{$item->deposit_trade_id}}">{{ $item->deposit_trade_id }}</td>
                                    <td title="{{ $item->sys_crypto_wallet_title }}"><strong>{{ str_limit($item->sys_crypto_wallet_title,15) }}</strong></td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true" width="auto">
                                            <div class="modal-dialog" role="document" width="auto">
                                                <div class="modal-content" width="auto">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{!!  '<i class="fontello-user-1"></i>'.$item->username.'&nbsp;&nbsp;<i class="fontello-phone"></i>'.$item->phone.'&nbsp;&nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;'.$item->deposit_trade_id !!}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{--凭证开放路由--}}
                                                        <img id="preview" src="{{url('')}}/{{ $item->deposit_proof_img }}" style="width:570px"
                                                             onerror="this.src='http://placehold.it/570x420'"/>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="label label-{{ $orderStatus[$item->deposit_order_status]['class'] }}">{{ $orderStatus[$item->deposit_order_status]['name'] }}</span>
                                    </td>

                                    <td>
                                        <a data-toggle="dropdown" class="dropdown-toggle" type="button">
                                            <span class="box-btn"><i class="fa fa-exchange" title="修改订单状态"></i></span>
                                        </a>
                                        {{--TODO 订单下拉状态样式调整--}}
                                        <ul role="menu" class="dropdown-menu">
                                            @foreach($orderStatus as $flag=>$status)
                                                <li>
                                                    @if($item->deposit_order_status != $flag)
                                                    <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                            '{{ url("order/userDeposit/$item->id") }}','deposit_order_status',{{$flag}},
                                                            '充值订单为<b><strong> {{$status['name']}} </strong></b> 状态',
                                                            '{{ csrf_token() }}');">
                                                    {{$status['name']}}</a>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("order/userDeposit/$item->id") }}',
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
                                    {{ $userDepositOrder->appends(Request::except('page'))->links() }}
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
