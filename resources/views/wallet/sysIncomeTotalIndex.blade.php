@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        @include('component.conditionSearch', ['url'=>url('otc/sys/income/total')])
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统总计收益查询</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <form action="{{ url('otc/sys/income/total')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--搜索--}}
                            {{--筛选商户--}}
                            <div class="col-sm-6">
                                <select class="flter-status form-control input-sm" id="searchMerchant" name="searchMerchant">
                                    <option value="" {{ !Request::get('searchMerchant') ? 'selected':'' }}>请选择商户</option>
                                    @foreach($searchMerchants as $key => $item)
                                        <option value="{{$item->id}}" {{ Request::get('searchMerchant')==$item->id
                                            ? 'selected' : ''}}>{{ $item->phone }} - {{ $item->username }}</option>
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
                                <th>商户</th>
                                <th>类型</th>
                                <th>联系方式</th>
                                <th>入金收益({{config('conf.currency_usdt')}})</th>
                                <th>出金收益({{config('conf.currency_usdt')}})</th>
                            </tr>
                            @forelse($merchants as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($merchants->currentPage() - 1) * $merchants->perPage() }}</td>
                                    <td>#{{ $item->id ?: '--' }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit(@$item->username ?:'--',11) }}</strong></td>
                                    <td>{{ @$merchantType[@$item->appKey->type]['name'] ??'--' }}</td>
                                    <td title="{{$item->phone ?:$item->email}}">{{ str_limit($item->phone ?:$item->email ?:'--' ,13) }}</td>
                                    <td>{{ floatval(@$item->income['in']) }}</td>
                                    <td>{{ floatval(@$item->income['out']) }}</td>
                                </tr>
                            @empty
                                @include('component.noData', ['colSpan'=>7])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <hr>
                                <div class="pull-left">
                                    {{--类型，1入金收益，2出金收益， 3充值手续费--}}
                                    @if(Request::get('searchMerchant'))
                                        总计： <b>{{ bcadd( $statistics['inTotal'] ?:0, $statistics['outTotal'] ?:0) }} {{config('conf.currency_usdt')}}</b><br>
                                    @else
                                        总计： <b>{{ $statistics['total'] ?: 0 }} {{config('conf.currency_usdt')}}</b><br>
                                    @endif
                                    入金收益：<b>{{ $statistics['inTotal'] ?: 0 }}</b> |
                                    出金收益：<b>{{ $statistics['outTotal'] ?: 0 }}</b>
                                    @if(!Request::get('searchMerchant'))
                                        | 充值手续费： <b>{{ $statistics['transFee'] ?: 0 }}</b>
                                    @endif

                                </div>

                                <div class="pull-right">
                                    {{ $merchants->appends(Request::except('page'))->links() }}
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
                var uri = '?searchMerchant='+$('#searchMerchant').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }


            $('#export').click(function () {
            });
        })
    </script>
@endsection
