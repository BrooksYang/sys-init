@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        @include('component.conditionSearch', ['url'=>url('wallet/transaction')])
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户钱包交易记录列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <form action="{{ url('wallet/transaction')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--搜索--}}
                            {{--用户基本信息--}}
                            <div class="col-sm-6">
                                <input class="form-control input-sm"  placeholder="请输入用户名、电话、转账地址"
                                       name="search" id="search" type="text" value="{{ Request::get('search')?? '' }}"/>
                            </div>

                            {{--币种--}}
                            <div class="col-sm-2">
                                <select class="filter-status form-control input-sm" id="filterCurrency" name="filterCurrency">
                                    <option value="">请选择币种</option>
                                    @foreach($currencies as $key => $currency)
                                        <option value="{{$key}}" {{ Request::get('filterCurrency')==$key ? 'selected' :''}}>{{ $currency }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{--交易记录类型--}}
                            <div class="col-sm-2">
                                <select class="filter-status form-control input-sm" id="filterType" name="filterType">
                                    <option value="">请选择类型</option>
                                    @foreach($type as $key => $itemType)
                                        <option value="{{$key}}" {{ Request::get('filterType')==$key ? 'selected' :''}}>{{ $itemType['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{--交易记录状态--}}
                            <div class="col-sm-2">
                                <select class="filter-status form-control input-sm" id="filterStatus" name="filterStatus">
                                    <option value="">请选择状态</option>
                                    @foreach($status as $key => $itemStatus)
                                        <option value="{{$key}}" {{ Request::get('filterStatus')==$key ? 'selected' :''}}>
                                            {{ $itemStatus['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </form>

                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>联系方式</th>
                                <th>币种</th>
                                <th>金额</th>
                                <th>手续费</th>
                                <th>转账地址</th>
                                <th>收款地址</th>
                                <th>交易号</th>
                                <th>NeuTxid</th>
                                <th>备注</th>
                                <th>类型</th>
                                <th>状态</th>
                                <th>创建时间
                                    @include('component.sort', ['url'=>'wallet/transaction'])
                                </th>
                            </tr>
                            @forelse($transDetails as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($transDetails->currentPage() - 1) * $transDetails->perPage() }}</td>
                                    <td title="{{ @$item->user->username }}"><strong>{{ str_limit(@$item->user->username ?:'--',15) }}</strong></td>
                                    <td title="{{@$item->user->email ?:@$item->user->phone}}">
                                        {{ str_limit(@$item->user->phone ?:@$item->user->email ,15) }}
                                    </td>
                                    <td><span class="label label-success">{{ str_limit(@$item->currency->currency_title_en_abbr,15) }}</span></td>
                                    <td>{{ $item->amount}}</td>
                                    <td>{{ $item->fee }}</td>
                                    <td title="{{ $item->from }}"><strong>{{ str_limit($item->from ?: '--',20) }}</strong></td>
                                    <td title="{{ $item->to }}"><strong>{{ str_limit($item->to ?: '--',20) }}</strong></td>
                                    <td title="{{ $item->hash }}"><strong>{{ str_limit($item->hash ?: '--',15) }}</strong></td>
                                    <td>{{ $item->neu_txid }}</td>
                                    {{--备注--}}
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">
                                                            {!!  '<i class="fontello-user-1"></i>'.@$item->user->username.'&nbsp;&nbsp;
                                                            <i class="fontello-phone"></i>'.@$item->user->phone ?:@$item->user->email  !!}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <P>备注信息：{{ $item->remark ?: '暂无'}}</P>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="">{{ $type[$item->type]['name'] }}</span></td>
                                    <td>
                                        <span class="label label-{{ $status[$item->status]['class'] }}">{{ $status[$item->status]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                </tr>
                            @empty
                                @include('component.noData', ['colSpan'=>14])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                @if($search)
                                    <hr>
                                    <div class="pull-left">
                                        {{--类型，1充值，2提现--}}
                                        总计： <b>{{ $statistics['total'] ?: 0 }}</b> &nbsp;&nbsp<b>{{ $transDetails->total() }}</b>&nbsp;单<br>
                                        充值：<b>{{ $statistics['transDeposit'] ?: 0 }}</b> |
                                        提币： <b>{{ $statistics['transWithDraw'] ?: 0 }}</b>
                                    </div>
                                @endif
                                <div class="pull-right">
                                    {{ $transDetails->appends(Request::except('page'))->links() }}
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
        $(function(){

            //按钮搜索
            $('#conditionSearch').click(function () {
                var uri = implodeUri();
                $(this).attr('href', uri);
            });

            // 回车搜索
            $("#searchForm").bind("keypress",function(e){
                // 兼容FF和IE和Opera
                var theEvent = e || window.event;
                var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
                if (code == 13) {
                    console.log('{{ Request::url() }}');
                    e.preventDefault();
                    //回车执行查询
                    var uri = implodeUri();
                    window.location.href = '{{ Request::url() }}'+uri;
                }
            });

            // 整理uri
            function implodeUri() {
                var uri = '?search='+$('#search').val()
                    +'&filterCurrency='+$('#filterCurrency').val()
                    +'&filterType='+$('#filterType').val()
                    +'&filterStatus='+$('#filterStatus').val();

                return uri;
            }


            $('#export').click(function () {
            });
        })
    </script>
@endsection
