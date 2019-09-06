@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right" style="margin: 20px 20px;">
                        @include('component.conditionSearch', ['url'=>url('sys/income?type=deposit')])

                        <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" style="margin-left: 10px;">
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

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 充值手续费收入列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <form action="{{ url('sys/income?type=deposit')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--搜索--}}
                            {{--用户基本信息--}}
                            <div class="col-sm-4">
                                <input class="form-control input-sm"  placeholder="请输入用户名、电话或邮箱信息"
                                       name="search" id="search" type="text" value="{{ Request::get('search')?? '' }}"/>
                            </div>

                            {{--转账地址--}}
                            <div class="col-sm-4">
                                <input class="form-control input-sm"  placeholder="请输入转账地址"
                                       name="searchFrom" id="searchFrom" type="text" value="{{ Request::get('from')?? '' }}"/>
                            </div>

                            {{--收款地址--}}
                            <div class="col-sm-4">
                                <input class="form-control input-sm"  placeholder="请输入收款地址"
                                       name="searchTo" id="searchTo" type="text" value="{{ Request::get('to')?? '' }}"/>
                            </div>
                        </div>
                        <div class="row">
                            {{--币种--}}
                            <div class="col-sm-3">
                                <select class="filter-status form-control input-sm" id="filterCurrency" name="filterCurrency">
                                    <option value="">请选择币种</option>
                                    @foreach($currencies as $key => $currency)
                                        <option value="{{$key}}"
                                                {{ Request::get('filterCurrency')==$key ? 'selected' : ($key==\App\Models\Currency::USDT ? 'selected':'')}}>
                                            {{ $currency }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{--交易记录类型--}}
                            <div class="col-sm-3">
                                <select class="filter-status form-control input-sm" id="filterType" name="filterType">
                                    <option value="">请选择类型</option>
                                    @foreach($type as $key => $itemType)
                                        <option value="{{$key}}" {{ Request::get('filterType')==$key ? 'selected'
                                            :($key==\App\Models\Wallet\WalletTransaction::DEPOSIT ? 'selected':'')}}>
                                            {{ $itemType['name'] }}</option>
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
                                <th>UID</th>
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
                                    @include('component.sort', ['url'=>'sys/income?type=deposit'])
                                </th>
                            </tr>
                            @forelse($transDetails as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($transDetails->currentPage() - 1) * $transDetails->perPage() }}</td>
                                    <td>#{{ $item->user_id ?: '--' }}</td>
                                    <td title="{{ @$item->user->username }}"><strong>{{ str_limit(@$item->user->username ?:'--',11) }}</strong></td>
                                    <td title="{{@$item->user->email ?:@$item->user->phone}}">
                                        {{ str_limit(@$item->user->phone ?:@$item->user->email ,13) }}
                                    </td>
                                    <td>{{ str_limit(@$item->currency->currency_title_en_abbr,15) }}</td>
                                    <td>{{ $item->amount}}</td>
                                    <td style="color: #0AA699"><strong>{{ $item->fee }}</strong></td>
                                    <td title="{{ $item->from }}" id="copyFrom{{$key}}" data-attr="{{$item->from}}">
                                        @if($item->from)
                                            @include('component.copy', ['eleId'=>'copyFrom'.$key, 'eleType'=>'attr', 'attr'=>'data-attr'])
                                        @endif
                                        <strong>{{ str_limit($item->from ?: '--',20) }}</strong>
                                    </td>
                                    <td title="{{ $item->to }}" id="copyTo{{$key}}" data-attr="{{$item->to}}">
                                        @if($item->to)
                                            @include('component.copy', ['eleId'=>'copyTo'.$key, 'eleType'=>'attr', 'attr'=>'data-attr'])
                                        @endif
                                        <strong>{{ str_limit($item->to ?: '--',20) }}</strong>
                                    </td>
                                    <td title="{{ $item->hash }}" id="copyHash{{$key}}" data-attr="{{$item->hash}}">
                                        @if($item->hash)
                                            @include('component.copy', ['eleId'=>'copyHash'.$key, 'eleType'=>'attr', 'attr'=>'data-attr'])
                                        @endif
                                        <strong><a {!! $item->hash ? 'href="https://etherscan.io/tx/'.$item->hash.'"'.' target="_blank"' : '####' !!}>
                                                {{ str_limit($item->hash ?: '--',15) }}</a></strong>
                                    </td>
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
                                    <td>{{ $status[$item->status]['name'] }}</td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                </tr>
                            @empty
                                @include('component.noData', ['colSpan'=>15])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                @if($search)
                                    <hr>
                                    <div class="pull-left">
                                        {{--类型，1充值，2提现--}}
                                        总计： <b>交易额 {{ $statistics['totalAmount'] ?: 0 }}</b> &nbsp;&nbsp
                                               <b>手续费 {{ $statistics['totalFee'] ?: 0 }}</b> &nbsp;&nbsp<b>{{ $transDetails->total() }}</b>&nbsp;单<br>
                                        充值：<b>{{ $statistics['transDeposit'] ?: 0 }}</b> |
                                        提币： <b>{{ $statistics['transWithDraw'] ?: 0 }}</b> |
                                        充值手续费： <b>{{ $statistics['depositFee'] ?: 0 }}</b> |
                                        提币手续费： <b>{{ $statistics['withDrawFee'] ?: 0 }}</b>
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

            if('{{ $errors->first()}}'){ layer.msg('{{ $errors->first()}}'); }

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

            // 整理uri
            function implodeUri() {
                var uri = '?search='+$('#search').val()
                    +'&from='+$('#searchFrom').val()
                    +'&to='+$('#searchTo').val()
                    +'&filterCurrency='+$('#filterCurrency').val()
                    +'&filterType='+$('#filterType').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val()
                    +'&type=deposit';
                    /*+'&filterStatus='+$('#filterStatus').val();*/

                return uri;
            }


            $('#export').click(function () {
            });
        })
    </script>
@endsection
