@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户 OTC 交易订单列表</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right box-tools">
                        @include('component.conditionSearch', ['url'=>url('order/otc')])
                    </div>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    {{--多条件搜索--}}
                    <form action="{{ url('order/otc')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--类型--}}
                            <div class="col-sm-1">
                                <select class="flter-status form-control input-sm" id="filterType" name="filterType">
                                    @foreach($orderType as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('filterType')==$key
                                            ? 'selected' : (!Request::get('filterType') && $item['name']=='买单' ? 'selected':'')}}>{{ $item['name'] }} </option>
                                    @endforeach
                                </select>
                            </div>
                            {{--用户名或电话或邮箱--}}
                            <div class="col-sm-4">
                                <input class="form-control input-sm"  placeholder="搜索用户名或邮箱或电话" name="searchUser" id="searchUser" type="text"
                                       value="{{ Request::get('searchUser')?? '' }}"/>
                            </div>
                            {{--OTC订单--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="OTC订单号" name="searchOtc" id="searchOtc" type="text"
                                       value="{{ Request::get('searchOtc')?? '' }}"/>
                            </div>
                            {{--商户订单--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="商户订单号" name="searchMerchant" id="searchMerchant" type="text"
                                       value="{{ Request::get('searchMerchant') ?? '' }}" />
                            </div>
                            {{--币种--}}
                            <div class="col-sm-1">
                                <select class="flter-status form-control input-sm" id="searchCurrency" name="searchCurrency">
                                    @foreach($currencies as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('searchCurrency')==$key
                                            ? 'selected' : (!Request::get('searchCurrency') && $item=='USDT' ? 'selected':'')}}>{{ $item }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom:10px;">
                            {{--订单状态--}}
                            <div class="col-sm-4">
                                <select class="flter-status form-control input-sm" id="filterStatus" name="filterStatus">
                                    <option value="">请选择订单状态</option>
                                    @foreach($orderStatus as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('filterStatus')==$key ? 'selected' :''}}>{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @include('component.dateTimePicker', ['colMdNum'=>4, 'id'=>1, 'label'=>'','name'=>'start','placeholder'=>'请选择开始时间'])
                            @include('component.dateTimePicker', ['colMdNum'=>4, 'id'=>2, 'label'=>'','name'=>'end','placeholder'=>'请选择结束时间'])
                        </div>
                    </form>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>OTC订单</th>
                                <th>用户名</th>
                                {{--<th>广告用户</th>--}}
                                <th>类型</th>
                                <th>币种</th>
                                <th>单价</th>
                                <th>法币</th>
                                <th>数量</th>
                                <th>总价</th>
                                <th>状态</th>
                                <th>申诉</th>
                                <th>商户订单</th>
                                <th>回调地址</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('order/otc')}}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :'' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('order/otc') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($userOtcOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcOrder->currentPage() - 1) * $userOtcOrder->perPage() }}</td>
                                    <td>#{{ $item->id }}</td>
                                    <td title="电话：{{$item->phone ?: $item->email}}">
                                        <strong>{{ str_limit($item->username ? $item->username : ($item->phone ? $item->phone :$item->email),15) }}</strong></td>
                                   {{-- <td title="{{ $item->from_username }} 电话：{{$item->from_user_phone}}"><strong>{{ str_limit($item->from_username,15) }}</strong></td>--}}
                                    <td>
                                        <span class="label label-{{ $orderType[$item->type]['class'] }}">{{ $orderType[$item->type]['name'] }}</span>
                                    </td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="">{{ str_limit($item->currency_title_en_abbr,15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->price,8,'.',',') }}">{{ number_format($item->price,8,'.',',') }}</td>
                                    <td title="{{ $item->name }}">{{ $item->abbr }}</td>
                                    <td title="{{number_format($item->field_amount,8,'.',',') }}">{{ number_format($item->field_amount,8,'.',',') }}</td>
                                    <td title="{{number_format($item->cash_amount,8,'.',',') }}">{{ number_format($item->cash_amount,8,'.',',') }}</td>
                                    <td><span class="label label-{{ $orderStatus[$item->status]['class'] ??''}}">
                                            {{ $orderStatus[$item->status]['name'] ?? '--'}}</span>
                                    </td>
                                    <td><span class="{{ $item->appeal_status ? "label label-".$appealStatus[$item->appeal_status]['class'] : '' }}">
                                            {{ $appealStatus[$item->appeal_status]['name'] ?? '--'}}</span>
                                    </td>
                                    <td>{{ $item->merchant_order_id ?:'--'}}</td>
                                    <td title="{{ $item->merchant_callback }}">{{ str_limit($item->merchant_callback ?:'--', 20) }}</td>
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
                                @if($search)
                                    <hr>
                                    <div class="pull-left">
                                        {{--交易总数量，交易总价--}}
                                        总计： <b>{{ $userOtcOrder->total() }}</b>&nbsp;单<br>
                                        交易总数量： <b>{{ $statistics['totalFieldAmount'] ?: 0 }}</b> |
                                        交易总价： <b>{{ $statistics['totalCashAmount'] ?: 0 }}</b>
                                    </div>
                                @endif
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
        $(function () {
            //日期时间插件
            $('#datetimepicker1').datetimepicker({
                language: 'zh'
            });

            //日期时间插件
            $('#datetimepicker2').datetimepicker({
                language: 'zh'
            });

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

            // 整理uri searchTeam
            function implodeUri() {
                var uri = '?searchUser='+$('#searchUser').val()
                    +'&searchOtc='+$('#searchOtc').val()
                    +'&searchMerchant='+$('#searchMerchant').val()
                    +'&searchCurrency='+$('#searchCurrency').val()
                    +'&filterStatus='+$('#filterStatus').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }
        })
    </script>
@endsection
