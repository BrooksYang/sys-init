@extends('entrance::layouts.default')

@section('css-part')
    @parent
    @include('component.dateTimeStyle')
    @include('component.hbfont')
@endsection

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
                        @include('component.filter', ['url'=>url('order/otc/withdraw'), 'filters'=>$orderStatus, 'filter'=>'status','title'=>'筛选状态', 'isInline'=>true])
                        @include('component.filter', ['url'=>url('order/otc/withdraw'), 'filters'=>$from, 'filter'=>'from','title'=>'筛选来源', 'icon'=>'fontello-menu', 'isInline'=>true])
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 用户提币订单待受理列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        {{--搜索筛选-导出--}}
                        <form action="{{ url('order/otc/withdraw/exportExcel') }}" class="in-block">
                            <div class="col-mg-12">
                                @include('component.dateTimePicker', ['colMdNum'=>4, 'id'=>1, 'label'=>'提现开始时间','name'=>'start_time','placeholder'=>'请选择要导出的提现开始时间'])
                                @include('component.dateTimePicker', ['colMdNum'=>4, 'id'=>2, 'label'=>'提现结束时间','name'=>'end_time','placeholder'=>'请选择要导出的提现结束时间'])
                                <div class="col-md-2">
                                    <label>(未选择日期默认导出全部)</label>
                                    <div class="form-group">
                                        <div class="input-group date">
                                            <button class="btn btn-info pull-right">按日期导出USDT提现记录</button>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-2">
                                    <label>(按周导出提现记录)</label>
                                    <div class="form-group">
                                        <div class="input-group date">
                                            <a href="{{ url('order/otc/withdraw') }}?byWeek=withdrawWeekExportExcel"
                                               class="btn btn-success pull-right">按周导出USDT提现记录</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>电话</th>
                                <th>来源</th>
                                <th>币种</th>
                                <th>提币金额</th>
                                <th>汇率(RMB)</th>
                                <th>RMB</th>
                                <th>手续费比例(%)</th>
                                <th>手续费</th>
                                <th>支付方式</th>
                                <th>收款账号</th>
                                @if(config('app.otc_withdraw_currency'))
                                <th title="收币钱包地址">收币地址</th>
                                @endif
                                <th>状态</th>
                                <th>创建时间
                                    @include('component.sort',['url' => url('order/otc/withdraw')])
                                <th>操作</th>
                            </tr>
                            @forelse($userOtcWithdrawOrder as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcWithdrawOrder->currentPage() - 1) * $userOtcWithdrawOrder->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->phone}}">{{ $item->phone }}</td>
                                    <td>{{ $from[$item->from]['name'] ?? '--' }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->amount,8,'.',',') }}">{{ number_format($item->amount,8,'.',',') }}</td>
                                    <td title="{{number_format($item->rate ?:0,8) }}">{{ number_format($item->rate ?:0,8) }}</td>
                                    <td title="{{number_format($item->rmb ?:0,8) }}">{{ number_format($item->rmb ?:0,8) }}</td>
                                    <td title="手续费比例">{{ $item->fee_percentage ?: 0 }}</td>
                                    <td title="手续费">{{number_format($item->fee ?:0,8) }}</td>
                                    {{--支付方式和账号--}}
                                    <?php $payType = \App\Models\OTC\OtcPayType::find($item->pay_type_id); ?>
                                    <td class="hbfont">
                                        <i class="{{ $payType->icon ?? '' }}" title="{{ $payType->name ?? '--' }}"></i>
                                    </td>
                                    <td title="{{ str_limit($item->account ?:'--', 25) }}">
                                        {{ str_limit($item->account ?:'--', 25) }}
                                        @if($item->account)
                                            <!-- Button trigger modal -->
                                                <a href="javascript:;"  class="ajaxPayAccount" data-toggle="modal" data-target="#exampleModalLongPayAccount{{$key}}" data-pay-user="{{$item->user_id}}" data-pay-account="{{ $item->account }}" data-key="{{$item->uid}}" title="开户信息">
                                                    &nbsp;<i class="fa fa-info-circle"></i>
                                                </a>
                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModalLongPayAccount{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongPayAccountTitle{{$key}}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" >账户信息</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div id="ajaxPayAccountDiv{{$item->uid}}"></div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                    </td>
                                    @if(config('app.otc_withdraw_currency'))
                                    <td title="{{ $item->crypto_wallet_address }}"><strong>{{ str_limit($item->crypto_wallet_address,15) ?:'--' }}</strong></td>
                                    @endif
                                    <td>
                                        <span class="label label-{{ $orderStatus[$item->status]['class'] }}">{{ $orderStatus[$item->status]['name'] }}</span>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                    <td>
                                        @if($item->status != \App\Models\OTC\OtcWithdraw::OTC_RELEASED )
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->uid }}',
                                                    '{{ url("order/otc/withdraw/$item->uid") }}','status',2,
                                                    ' OTC 提币订单为<b><strong> 处理中 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '处理中' );" title="处理中"> <i class=" fontello-loop"></i> </a>
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->uid }}',
                                                    '{{ url("order/otc/withdraw/$item->uid") }}','status',3,
                                                    ' OTC 提币订单为<b><strong> 已发币 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '已发币' );" title="已发币"> <i class="fontello-ok"></i>
                                            </a><a href="javascript:;" onclick="itemUpdate('{{ $item->uid }}',
                                                    '{{ url("order/otc/withdraw/$item->uid") }}','status',4,
                                                    ' OTC 提币订单为<b><strong> 失败 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '失败' );" title="失败"> <i class="fontello-reply"></i> </a>
                                        @else
                                            '--'
                                        @endif
                                        {{--<a href="javascript:;" onclick="itemDelete('{{ $item->uid }}',
                                                '{{ url("order/otc/withdraw/$item->uid") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>--}}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ config('app.otc_withdraw_currency') ? 16 : 15 }}" class="text-center">
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
    @include('component.dateTimeScript')
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

            /* $("#datetimepicker1").datetimepicker({
             format: 'yyyy-mm-dd',//显示格式
             todayHighlight: 1,//今天高亮
             minView: "month",//设置只显示到月份
             startView:2,
             forceParse: 0,
             showMeridian: 1,
             autoclose: 1//选择后自动关闭
             });*/

            // 支付账号-开户行信息
            $('.ajaxPayAccount').click(function () {

                var key = $(this).attr('data-key');
                if (!$('#ajaxPayAccountDiv'+key).html().length) {

                    var payUserId = $(this).attr('data-pay-user');
                    var payAccount = $(this).attr('data-pay-account');

                    $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
                    $.ajax({
                        type: "POST",
                        cache: false,
                        url: "{{ url('payUser/Account') }}",
                        data: {"payUserId": payUserId,"payAccount": payAccount},
                        dataType: "json",
                        success: function (data) {
                            //console.log(data[0].payName ?data[0].payName:'暂无');
                            var payName = data.name ? data.name : '暂无';

                            var payBank = data.bank ? data.bank : '暂无';
                            var payBankBranch = data.bank_branch ? data.bank_branch : '暂无';
                            var payBankAddr = data.bank_address ? data.bank_address : '暂无';

                            var payAccountInfo =
                                `<p>账号持有者姓名：` + payName + `</p>` +
                                `<p>开户银行：` + payBank + `</p>` +
                                `<p>支行：` + payBankBranch + `</p>` +
                                `<p>开户行地址：` + payBankAddr + '</p>';

                            $('#ajaxPayAccountDiv'+key).html(payAccountInfo);

                        },
                        error: function (data) {
                            layer.msg('网络错误')
                        }
                    });
                }

            });
        })
    </script>
@endsection
