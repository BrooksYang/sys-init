@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 币商快捷抢单收益</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right" style="margin: 20px 20px;">
                        @include('component.conditionSearch', ['url'=>url('otc/sys/income')])
                    </div>

                </div>

                {{-- Table --}}
                <div class="box-body">
                    {{--多条件搜索--}}
                    <form action="{{ url('otc/sys/income')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--币商用户名或电话或邮箱--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="搜索币商用户名或邮箱或电话" name="searchUser" id="searchUser" type="text"
                                       value="{{ Request::get('searchUser')?? '' }}"/>
                            </div>
                            {{--订单发布者用户名称或电话--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="发布者电话" name="searchFromUser" id="searchFromUser" type="text"
                                       value="{{ Request::get('searchFromUser')?? '' }}"/>
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
                            {{--OTC 快捷抢单订单--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="OTC订单号" name="searchOtc" id="searchOtc" type="text"
                                       value="{{ Request::get('searchOtc')?? '' }}"/>
                            </div>
                            {{--商户订单--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="商户订单号" name="searchMerchant" id="searchMerchant" type="text"
                                       value="{{ Request::get('searchMerchant') ?? '' }}" />
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            {{--订单状态--}}
                            <div class="col-sm-2">
                                <select class="flter-status form-control input-sm" id="filterStatus" name="filterStatus">
                                    <option value="">请选择订单状态</option>
                                    @foreach($orderStatus as $key => $item)
                                        <option value="{{$key}}" {{ Request::get('filterStatus')==$key ||
                                           (Request::path()=='otc/sys/income' && $key ==\App\Models\OTC\OtcOrderQuick::RECEIVED) ? 'selected' :''}}>
                                            {{ $item['name'] }}</option>
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
                            {{--商户用户名称或电话--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="商户用户名或电话" name="searchMerchant" id="searchMerchant" type="text"
                                       value="{{ Request::get('searchMerchant')?? '' }}"/>
                            </div>
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>1, 'label'=>'','name'=>'start','placeholder'=>'请选择开始时间'])
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>2, 'label'=>'','name'=>'end','placeholder'=>'请选择结束时间'])
                        </div>
                    </form>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>订单</th>
                                <th>币商</th>
                                <th>发布者</th>
                                <th>发布数量</th>
                                {{--<th title="商户结算数量">结算数量</th>--}}
                                <th>发布汇率</th>
                                <th>总价</th>
                                <th>交易数量</th>
                                <th>币商价</th>
                                <th>平台汇率</th>
                                {{--<th>总收益</th>--}}
                                <th>平台收益</th>
                                <th>商户收益</th>
                                <th>币商收益</th>
                                <th>备注</th>
                                <th>卡号</th>
                                <th>凭证</th>
                                <th>状态</th>
                                <th>申诉</th>
                                <th>商户订单</th>
                                {{--<th>商户</th>--}}
                                <th>创建时间
                                    @include('component.sort', ['url'=>url('otc/sys/income')])
                                </th>
                            </tr>
                            @forelse($otcQuickOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($otcQuickOrder->currentPage() - 1) * $otcQuickOrder->perPage() }}</td>
                                    <td>#{{ $item->id }}</td>
                                    <td title="UID：{{ $item->user_id }} | 联系方式：{{ @$item->user->phone ?: @$item->user->email}}">
                                        <strong>{{ str_limit(@$item->user->username ?: (@$item->user->phone ?:@$item->user->email) ?:'--',10) }}</strong></td>
                                    <td>{{ str_limit($item->owner_phone ?:'--', 11) }}</td>
                                    <td>{{ number_format($item->merchant_amount, 8) }}</td>
                                    {{--<td title="商户结算数量">{{ number_format($item->merchant_final_amount, 8) }}</td>--}}
                                    <td>{{ number_format($item->merchant_rate, 2) }}</td>
                                    <td>{{ number_format($item->cash_amount, 8) }}</td>
                                    <td>{{ number_format($item->field_amount, 8) }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>{{ number_format($item->rate_sys, 2) }}</td>
                                    {{--<td>{{ number_format($item->income_total, 8) }}</td>--}}
                                    <td style="color: #0AA699"><strong>{{ number_format($item->income_sys, 8) }}</strong></td>
                                    <td>{{ number_format($item->income_merchant, 8) }}</td>
                                    <td>{{ number_format($item->income_user, 8) }}</td>
                                    <td title="{{$item->remark}}">{{ str_limit($item->remark ?: '--', 8) }}</td>
                                    <td title="{{$item->card_number}}">{{ str_limit($item->card_number ?: '--', 8) }}</td>

                                    <td >
                                        {{--凭证--}}
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}">
                                           <i class="fontello-ticket"></i>
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true" width="auto">
                                            <div class="modal-dialog" role="document" width="auto">
                                                <div class="modal-content" width="auto">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{!!  '<i class="fontello-user-1"></i>'.$item->username !!}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <span><b>【快捷订单】</b></span>#{{ $item->id }}
                                                        <p></p>
                                                        <span><b>【商户订单】</b></span>{{ $item->merchant_order_id ?:'--'}}
                                                        <span><b>【交易数量】</b></span>{{ $item->field_amount ?:'--'}}
                                                        <span><b>【交易总价】</b></span>{{ $item->cash_amount ?:'--'}}
                                                        <p></p>
                                                        <span><b>【备注】</b></span>{{ $item->remark ?:'--'}}
                                                        <span><b>【用户】</b></span>{{ str_limit($item->owner_phone ?:'--', 11) }}
                                                        <div style="height: 20px"></div>
                                                        {{--凭证开放路由--}}
                                                        <img id="" src="{{ config('app.api_res_url') }}/{{ $item->payment_url }}" style="width:570px;border-radius:20px"
                                                             onerror="this.src='http://placehold.it/570x922'" onclick="rotate(this)"/>
                                                        <p></p>
                                                    </div>
                                                    <div style="height: 55px"></div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $orderStatus[$item->status]['name'] ?? '--'}}</td>
                                    <td><span class="{{ $item->appeal_status ? "label label-".$appealStatus[$item->appeal_status]['class'] : '' }}">
                                            {{ $appealStatus[$item->appeal_status]['name'] ?? '--'}}</span>
                                    <td title="所属商户UID：{{ $item->merchant_id }} | 联系方式：{{ @$item->merchant->phone ?: @$item->merchant->email}}">
                                        {{ $item->merchant_order_id ?:'--'}}
                                    </td>
                                   {{-- <td title="UID：{{ $item->merchant_id }} | 联系方式：{{ @$item->merchant->phone ?: @$item->merchant->email}}">
                                        <strong>{{ str_limit(@$item->merchant->username ?: (@$item->merchant->phone ?:@$item->merchant->email),15) }}</strong></td>--}}
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="20" class="text-center">
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
                                        {{--交易总数量及收益--}}
                                        总计：交易总数量： <b>{{ number_format($statistics['totalFieldAmount'] ?: 0, 8)}}</b>&nbsp;,
                                            <b>{{ $otcQuickOrder->total() }}</b>&nbsp;单<br>
                                        总收益： <b>{{ number_format($statistics['totalIncome'] ?: 0, 8) }}</b> |
                                        平台收益：<b>{{ number_format($statistics['totalIncomeSys'] ?: 0, 8) }} </b> |
                                        商户收益：<b>{{ number_format($statistics['totalIncomeMerchant'] ?: 0, 8) }} </b>
                                    </div>
                                @endif
                                <div class="pull-right">
                                    {{ $otcQuickOrder->appends(Request::except('page'))->links() }}
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
                    +'&filterStatus='+$('#filterStatus').val()
                    +'&filterAppeal='+$('#filterAppeal').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val()
                    +'&type=orderQuick';

                return uri;
            }
        })
    </script>
@endsection