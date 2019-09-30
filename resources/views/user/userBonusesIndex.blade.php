@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户分润收益记录 -（UID: {{ $user->id }} | {{$user->username?:$user->phone?:$user->email}}）</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    {{--多条件搜索--}}
                    <form action="{{ url("user/trader/income/$user->id") }}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--贡献者用户名或电话或邮箱--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="搜索用户名或邮箱或电话" name="searchContributor" id="searchContributor" type="text"
                                       value="{{ Request::get('searchContributor')?? '' }}"/>
                            </div>
                            {{--备注--}}
                            <div class="col-sm-2">
                                <input class="form-control input-sm"  placeholder="备注" name="searchRemark" id="searchRemark" type="text"
                                       value="{{ Request::get('searchRemark')?? '' }}"/>
                            </div>

                            {{--交易id--}}
                            <div class="col-sm-1">
                                <input class="form-control input-sm"  placeholder="交易ID" name="searchTransaction" id="searchTransaction" type="text"
                                       value="{{ Request::get('searchTransaction')?? '' }}"/>
                            </div>
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>1, 'label'=>'','name'=>'start','placeholder'=>'请选择分润开始时间'])
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>2, 'label'=>'','name'=>'end','placeholder'=>'请选择分润结束时间'])
                        </div>

                    </form>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>贡献者UID</th>
                                <th>贡献者</th>
                                <th>贡献金额</th>
                                <th>交易时间</th>
                                <th>交易ID</th>
                                <th>交易金额</th>
                                <th>奖励百分比</th>
                                <th>奖励金额</th>
                                <th>备注</th>
                                <th>分润时间
                                    @include('component.sort',['url'=>url("user/trader/income/$user->id")])
                                </th>
                            </tr>
                            @forelse($bonuses as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($bonuses->currentPage() - 1) * $bonuses->perPage() }}</td>
                                    <td>#{{ $item->contributor_id ?: '--'}}</td>
                                    <td><strong>{{ str_limit(@$item->contributor->username ?:@$item->contributor->phone?:@$item->contributor->email,15) }}</strong></td>
                                    <td>{{ $item->total }}</td>
                                    <td>{{ $item->transaction_created_at }}</td>
                                    <td>TS-{{ $item->transaction_id }}</td>
                                    <td>{{ $item->transaction_amount }}</td>
                                    <td>{{ $item->percentage }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td title="{{ $item->remark }}">{{ str_limit($item->remark,10) }}</td>
                                    <td>{{ $item->created_at ?:'--' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">
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
                                        {{--交易总额，贡献总额--}}
                                        总计： <b>{{ $bonuses->total() }}</b>&nbsp;单<br>
                                        交易总金额： <b>{{ number_format($statistics['totalTransaction'] ?: 0, 8) }}</b> |
                                        奖励总金额： <b>{{ number_format($statistics['totalAmount'] ?: 0, 8)}}</b> |
                                        贡献总金额： <b>{{ number_format($statistics['totalContribution'] ?: 0, 8) }}</b>
                                    </div>
                                @endif
                                <div class="pull-right">
                                    {{ $bonuses->appends(Request::except('page'))->links() }}
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
                var uri = '?searchContributor='+$('#searchContributor').val()
                    +'&searchRemark='+$('#searchRemark').val()
                    +'&searchTransaction='+$('#searchTransaction').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }
        })
    </script>
@endsection
