@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>OTC 币商快捷抢单完成情况</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right" style="margin: 20px 20px;">
                        @include('component.conditionSearch', ['url'=>url('order/quick/otc/byTrader')])

                        <a href="{{url('order/quick/otc/byTrader')}}?reset=all" style="margin-right:12px;;color: darkgray;" class="btn btn-default" title="重置">
                            {{--重置搜索项--}}
                            <i class="fontello-cancel"></i>&nbsp;&nbsp;重置
                        </a>

                        <a href="{{url('order/quick/otc')}}" style="margin-right:12px;;color: darkgray;" class="btn btn-default" title="返回">
                            {{--返回快捷抢单--}}
                            <i class="fa fa-arrow-left"></i>&nbsp;&nbsp;返回
                        </a>
                    </div>

                </div>

                {{-- Table --}}
                <div class="box-body">
                    {{--多条件搜索--}}
                    <form action="{{ url('order/quick/otc/byTrader')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--筛选商户--}}
                            <div class="col-sm-3">
                                <select class="flter-status form-control input-sm" id="searchMerchant" name="searchMerchant">
                                    <option value="" {{ !Request::get('searchMerchant') ? 'selected':'' }}>请选择商户</option>
                                    @foreach($merchants as $key => $item)
                                        <option value="{{$item->id}}" title="{{$item->phone?:$item->email}}" {{ Request::get('searchMerchant')==$item->id
                                            ? 'selected' : ''}}>{{ $item->phone?:str_limit($item->email,10) }} - {{ $item->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{--币商用户名或电话或邮箱--}}
                            <div class="col-sm-3">
                                <input class="form-control input-sm"  placeholder="搜索币商用户名或邮箱或电话" name="searchUser" id="searchUser" type="text"
                                       value="{{ Request::get('searchUser')?? '' }}"/>
                            </div>
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>1, 'label'=>'','name'=>'start','placeholder'=>'请选择开始时间'])
                            @include('component.dateTimePicker', ['colMdNum'=>3, 'id'=>2, 'label'=>'','name'=>'end','placeholder'=>'请选择结束时间'])
                        </div>
                    </form>
                    <div class="box-body table-responsive no-padding">
                        <div class="row">
                            <div class="col-sm-4">系统待抢单：{{ @$data['unGrab']->get(0) ?: 0 }}</div>
                        </div>
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>UID</th>
                                <th>币商</th>
                                <th>待支付</th>
                                <th>已完成</th>
                                <th>处理中</th>
                                <th>总计</th>
                            </tr>
                            @forelse($data['total'] as $key => $item)
                                @if($item->user_id)
                                <tr>
                                    {{--<td>{{ ($key + 1) + ($data['total']->currentPage() - 1) * $data['total']->perPage() }}</td>--}}
                                    <td>{{ ($key) + ($data['total']->currentPage() - 1) * $data['total']->perPage() }}</td>
                                    <td>#{{ $item->user_id}}</td>
                                    <td title="联系方式：{{ @$item->user->phone ?:'--'}} | {{@$item->user->email ?:'--'}}">
                                        <strong>{{ str_limit(@$item->user->username ?: (@$item->user->phone ?:@$item->user->email) ?:'--',15) }}</strong>
                                    </td>
                                    <td style="color: #F56954">{{ @$data['unPay'][$item->user_id]->get($item->user_id) ?:0}}</td>
                                    <td style="color: #0AA699">{{ @$data['finished'][$item->user_id]->get($item->user_id) ?:0}}</td>
                                    <td style="color: #f0ad4e">{{ @$data['appealing'][$item->user_id]->get($item->user_id) ?:0}}</td>
                                    <td><strong>{{ $item->orders ?: 0}}</strong></td>
                                </tr>
                                @endif
                            @empty
                                <tr><td colspan="7" class="text-center">
                                        <div class="noDataValue">
                                            暂无数据
                                        </div>
                                    </td></tr>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                @if(@$search)
                                    <hr>
                                    <div class="pull-left">

                                    </div>
                                @endif
                                <div class="pull-right">
                                    {{ $data['total']->appends(Request::except('page'))->links() }}
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
                    +'&searchMerchantOrder='+$('#searchMerchantOrder').val()
                    +'&searchMerchant='+$('#searchMerchant').val()
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
