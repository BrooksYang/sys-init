@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 交易手续费收入列表</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right" style="margin: 20px 20px;">
                        @include('component.conditionSearch', ['url'=>url('otc/sys/income')])

                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
                            收益类型<span class="caret"></span>
                        </button>
                        <ul role="menu" class="dropdown-menu">
                            @foreach($incomeType as $key=>$item)
                                <li><a href="{{ $item['url'] }}">{{ $item['name'] }}
                                        @if ($key==1 && !Request::get('type'))
                                            &nbsp;<i class="fa fa-check txt-info"></i>
                                        @elseif($key==2 && Request::get('type')=='deposit')
                                            &nbsp;<i class="fa fa-check txt-info"></i>
                                        @endif
                                    </a></li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                {{-- Table --}}
                <div class="box-body">
                    {{--多条件搜索--}}
                    <form action="{{ url('otc/sys/income')}}" id="searchForm">
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
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="搜索用户名或邮箱或电话" name="searchUser" id="searchUser" type="text"
                                       value="{{ Request::get('searchUser')?? '' }}"/>
                            </div>
                            {{--备注--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="备注" name="searchRemark" id="searchRemark" type="text"
                                       value="{{ Request::get('searchRemark')?? '' }}"/>
                            </div>
                            {{--付款卡号--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="付款卡号" name="searchCardNumber" id="searchCardNumber" type="text"
                                       value="{{ Request::get('searchCardNumber')?? '' }}"/>
                            </div>
                            {{--OTC订单--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="OTC订单号" name="searchOtc" id="searchOtc" type="text"
                                       value="{{ Request::get('searchOtc')?? '' }}"/>
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
                            {{--商户订单--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="商户订单号" name="searchMerchant" id="searchMerchant" type="text"
                                       value="{{ Request::get('searchMerchant') ?? '' }}" />
                            </div>
                            {{--广告商名称或电话--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="搜索广告商名称或电话" name="searchFromUser" id="searchFromUser" type="text"
                                       value="{{ Request::get('searchFromUser')?? '' }}"/>
                            </div>
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>1, 'label'=>'','name'=>'start','placeholder'=>'请选择开始时间'])
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>2, 'label'=>'','name'=>'end','placeholder'=>'请选择结束时间'])
                        </div>
                    </form>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>OTC订单</th>
                                <th>用户名</th>
                                <th>备注</th>
                                <th>付款卡号</th>
                                <th>类型</th>
                                <th>币种</th>
                                <th>单价</th>
                                <th>法币</th>
                                <th>数量</th>
                                <th>总价</th>
                                <th>手续费({{ config('conf.currency_usdt') }})</th>
                                <th>实际到账</th>
                                <th>状态</th>
                                <th>申诉</th>
                                <th>商户订单</th>
                                <th>回调</th>
                                <th>广告商</th>
                                <th>创建时间
                                    @include('component.sort', ['url'=>url('otc/sys/income')])
                                </th>
                            </tr>
                            @forelse($userOtcOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcOrder->currentPage() - 1) * $userOtcOrder->perPage() }}</td>
                                    <td>#{{ $item->id }}</td>
                                    <td title="电话：{{$item->user->phone ?: $item->user->email}}">
                                        <strong>{{ str_limit($item->user->username
                                            ? @$item->user->username : (@$item->user->phone ? @$item->user->phone :@$item->user->email),15) }}</strong></td>
                                    <td title="{{$item->remark}}">{{ str_limit($item->remark ?: '--', 8) }}</td>
                                    <td title="{{$item->card_number}}">{{ str_limit($item->card_number ?: '--', 8) }}</td>
                                    <td>{{ $orderType[$item->type]['name'] }}</td>
                                    <td><span class="">{{ str_limit(@$item->currency->abbr,15) }}</span></td>
                                    <td>{{ number_format($item->price, 8) }}</td>
                                    <td title="{{ @$item->legalCurrency->name }}">{{ @$item->legalCurrency->abbr }}</td>
                                    <td>{{ number_format($item->field_amount, 8) }}</td>
                                    <td>{{ number_format($item->cash_amount, 8) }}</td>
                                    <td style="color: #0AA699"><strong>{{ number_format($item->fee, 8) }}</strong></td>
                                    <td>{{ number_format($item->final_amount, 8) }}</td>
                                    <td>{{ $orderStatus[$item->status]['name'] ?? '--'}}</td>
                                    <td>{{ $appealStatus[$item->appeal_status]['name'] ?? '--'}}</td>
                                    <td>{{ $item->merchant_order_id ?:'--'}}</td>
                                    <td title="{{ $item->merchant_callback }}"><i class="fontello-globe-1"></i></td>
                                    <td title="广告-{{ $item->advertisement_id }} | 广告商-{{ $item->from_user_id }} | 电话-{{ $item->f_phone }}">
                                        {{ str_limit(@$item->tradeOwner->username ?: @$item->tradeOwner->phone,11) }}
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="19" class="text-center">
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
                                        交易总数量： <b>{{ number_format($statistics['totalFieldAmount'] ?: 0, 8)}}</b> |
                                        交易总价： <b>{{ number_format($statistics['totalCashAmount'] ?: 0, 8) }}</b> |
                                        交易手续费：<b>{{ number_format($statistics['totalFee'] ?: 0, 8) }} {{ config('conf.currency_usdt') }}</b>
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
                    +'&searchFromUser='+$('#searchFromUser').val()
                    +'&searchRemark='+$('#searchRemark').val()
                    +'&searchCardNumber='+$('#searchCardNumber').val()
                    +'&searchOtc='+$('#searchOtc').val()
                    +'&searchMerchant='+$('#searchMerchant').val()
                    +'&searchCurrency='+$('#searchCurrency').val()
                    +'&filterType='+$('#filterType').val()
                   /* +'&filterStatus='+$('#filterStatus').val()
                    +'&filterAppeal='+$('#filterAppeal').val()*/
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }
        })
    </script>
@endsection