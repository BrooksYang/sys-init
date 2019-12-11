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
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="搜索用户名或邮箱或电话" name="searchUser" id="searchUser" type="text"
                                       value="{{ Request::get('searchUser')?? '' }}"/>
                            </div>
                            {{--备注--}}
                            <div class="col-sm-1">
                                <input class="form-control input-sm"  placeholder="备注" name="searchRemark" id="searchRemark" type="text"
                                       value="{{ Request::get('searchRemark')?? '' }}"/>
                            </div>
                            {{--付款卡号--}}
                            <div class="col-sm-1">
                                <input class="form-control input-sm"  placeholder="付款卡号" name="searchCardNumber" id="searchCardNumber" type="text"
                                       value="{{ Request::get('searchCardNumber')?? '' }}"/>
                            </div>
                            {{--OTC订单--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="OTC订单号" name="searchOtc" id="searchOtc" type="text"
                                       value="{{ Request::get('searchOtc')?? '' }}"/>
                            </div>
                            {{--商户订单--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="商户订单号" name="searchMerchantOrder" id="searchMerchantOrder" type="text"
                                       value="{{ Request::get('searchMerchantOrder') ?? '' }}" />
                            </div>
                            {{--广告上名称或电话--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="搜索广告商名称或电话" name="searchFromUser" id="searchFromUser" type="text"
                                       value="{{ Request::get('searchFromUser')?? '' }}"/>
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
                            <div class="col-sm-2">
                                <select class="flter-status form-control input-sm" id="filterStatus" name="filterStatus">
                                    <option value="">请选择订单状态</option>
                                    @foreach($orderStatus as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('filterStatus')==$key ? 'selected' :''}}>{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--申诉状态--}}
                            <div class="col-sm-2">
                                <select class="flter-status form-control input-sm" id="filterAppeal" name="filterAppeal">
                                    <option value="">请选择申诉状态</option>
                                    @foreach($appealStatus as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('filterAppeal')==$key ? 'selected' :''}}>{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--筛选商户--}}
                            <div class="col-sm-2">
                                <select class="flter-status form-control input-sm" id="searchMerchant" name="searchMerchant">
                                    <option value="" {{ !Request::get('searchMerchant') ? 'selected':'' }}>请选择商户</option>
                                    @foreach($merchants as $key => $item)
                                        <option value="{{$item->id}}" title="{{$item->phone?:$item->email}}" {{ Request::get('searchMerchant')==$item->id
                                            ? 'selected' : ''}}>{{ $item->phone?: str_limit($item->email,10) }} - {{ $item->username }}</option>
                                    @endforeach
                                </select>
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
                                <th title="团队红利">红利</th>
                                <th title="团队红利结算状态">结算</th>
                                <th>广告商</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('order/otc')}}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :'' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('order/otc') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                {{--<th>操作</th>--}}
                            </tr>
                            @forelse($userOtcOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcOrder->currentPage() - 1) * $userOtcOrder->perPage() }}</td>
                                    <td>#{{ $item->id }}</td>
                                    <td title="账号：{{@$item->user->email ?: @$item->user->phone}}">
                                        <strong>{{ str_limit(@$item->user->username ?: (@$item->user->phone ?:@$item->user->email),15) }}</strong></td>
                                    <td title="{{$item->remark}}">{{ str_limit($item->remark ?: '--', 8) }}</td>
                                    <td title="{{$item->card_number}}">{{ str_limit($item->card_number ?: '--', 8) }}</td>
                                    <td><span class="label label-{{ $orderType[$item->type]['class'] }}">{{ $orderType[$item->type]['name'] }}</span></td>
                                    <td><span class="">{{ str_limit(@$item->currency->abbr,15) }}</span></td>
                                    <td title="{{number_format($item->price)}}">{{ $item->price }}</td>
                                    <td title="{{ @$item->legalCurrency->name }}">{{ @$item->legalCurrency->abbr }}</td>
                                    <td title="{{number_format($item->field_amount, 8) }}">{{ $item->field_amount }}</td>
                                    <td title="{{number_format($item->cash_amount, 8) }}">{{ $item->cash_amount }}</td>
                                    <td title="{{number_format($item->fee, 8) }}">{{ $item->fee }}</td>
                                    <td title="{{number_format($item->final_amount, 8) }}">{{ $item->final_amount }}</td>
                                    <td><span class="label label-{{ $orderStatus[$item->status]['class'] ??''}}">
                                            {{ $orderStatus[$item->status]['name'] ?? '--'}}</span>
                                    </td>
                                    <td><span class="{{ $item->appeal_status ? "label label-".$appealStatus[$item->appeal_status]['class'] : '' }}">
                                            {{ $appealStatus[$item->appeal_status]['name'] ?? '--'}}</span>
                                    </td>
                                    <td title="商户订单：{{$item->merchant_order_id}} | 所属商户UID：{{$item->merchant_id}} | {{@$item->merchant->username?:'--'}} | {{@$item->merchant->phone ?: @$item->merchant->email}}">
                                        {{ $item->merchant_order_id ? substr_replace($item->merchant_order_id, '***', 4, 14):'--'}}
                                            <!-- Button trigger modal -->
                                            @if($item->merchant_currency)
                                                @include('component.modalHeader', ['modal'=>'Remark','title'=>'商户订单信息',
                                                    'header'=>'商户订单信息', 'icon'=>'fa fa-info-circle', 'color'=>'gray'])
                                                <p>商户币种：{{ $item->merchant_currency }}</p>
                                                <p>币种汇率：{{ $item->rate ?: '暂无' }}</p>
                                                <p>发币数量：{{ $item->send_amount }}</p>
                                                <p>用户地址：{{ $item->address ?: '暂无' }}</p>
                                                <p>交易哈希：{{ $item->hash ?: '暂无' }}</p>
                                                <p>状态：{{ @$hashStatus[$item->hash_status]['name'] ?: '暂无' }}</p>
                                                @include('component.modalFooter',['form'=>false]).
                                            @endif
                                    </td>
                                <td title="{{ $item->merchant_callback }}">
                                    <!-- Button trigger modal -->
                                    @include('component.modalHeader', ['modal'=>'callback','title'=>'商户回调信息',
                                        'header'=>'回调信息', 'icon'=>'fontello-globe-1', 'color'=>$item->is_callback?'black':'orange'])
                                    <p>商户回调接口：{{ $item->merchant_callback }}</p>
                                    <p>回调状态：{{ $item->is_callback ?'已回调':'未回调' }}</p>
                                    <p>手动回调响应：@if($item->callback_response)<?php dump(json_decode($item->callback_response,true))?>@else暂无@endif</p>
                                        @if(!$item->is_callback && !$item->merchant_currency)
                                        <a href="####" class="pull-right" style="color: orangered" onclick="itemUpdate('{{ $item->id }}',
                                                '{{ url("order/merchant-callback/$item->id") }}','call_back',true,
                                                '商户订单<b><strong> 回调 </strong></b> 状态',
                                                '{{ csrf_token() }}','回调');"><i class="icon-return"></i>回调</a><br>
                                        @endif
                                    @include('component.modalFooter',['form'=>false])
                                </td>
                                    <td title="{{number_format($item->team_bonus, 8) }}">{{ $item->team_bonus?:'--' }}</td>
                                    <td>{{ $item->team_bonus_status ?$teamBonusStatus[$item->team_bonus_status]:'--' }}</td>
                                    <td title="广告-{{ $item->advertisement_id }} | 广告商UID-{{ $item->from_user_id }} | {{ @$item->tradeOwner->username ?: @$item->tradeOwner->phone }}">
                                        {{ str_limit(@$item->tradeOwner->username ?: @$item->tradeOwner->phone,11) }}
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
                                       {{-- <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("order/otc/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>--}}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="21" class="text-center">
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
                                        交易手续费：<b>{{ number_format($statistics['totalFee'] ?: 0, 8) }} {{ config('conf.currency_usdt') }}</b> |
                                        团队红利：<b>{{ number_format($statistics['totalBonus'] ?: 0, 8) }}
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
                    +'&searchMerchantOrder='+$('#searchMerchantOrder').val()
                    +'&searchCurrency='+$('#searchCurrency').val()
                    +'&filterType='+$('#filterType').val()
                    +'&filterStatus='+$('#filterStatus').val()
                    +'&filterAppeal='+$('#filterAppeal').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }
        })
    </script>
@endsection
