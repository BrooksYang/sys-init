@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        @include('component.searchForm', ['url'=>url('wallet/balance/log'), 'placeholder'=>'搜索用户名或电话及邮箱'])
                        @include('component.filter', ['url'=>url('wallet/balance/log'), 'filters'=>$type, 'title'=>'筛选类型'])
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户记账钱包余额划转记录</span>
                    </h3>
                </div>


                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>UID</th>
                                <th>用户名称</th>
                                <th>邮箱账号</th>
                                <th>电话</th>
                                <th>币种</th>
                                <th>类型</th>
                                <th>变更数额</th>
                                <th>变更前</th>
                                <th>变更后</th>
                                <th>备注</th>
                                <th>变更时间
                                    @include('component.sort',['url'=>url('wallet/balance/log')])
                                </th>
                                <th>恢复</th>
                                <th>操作</th>
                            </tr>
                            @forelse($balanceLog as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($balanceLog->currentPage() - 1) * $balanceLog->perPage() }}</td>
                                    <td>#{{ $item->user_id ?: '--'}}</td>
                                    <td title="{{@$item->user->username}}"><strong>{{ str_limit(@$item->user->username ?:'--',15) }}</strong></td>
                                    <td title="{{@$item->user->email}}">{{ str_limit(@$item->user->email ?:'--',20) }}</td>
                                    <td>{{ str_limit(@$item->user->phone ?:'--',11) }}</td>
                                    <td><span class="label label-success">{{ str_limit(@$item->currency->abbr,8) }}</span></td>
                                    <td>{{ @$type[$item->type]['name'] }}</td>
                                    <td title="{{number_format($item->amount, 8)}}">{{$item->amount }}</td>
                                    <td title="{{number_format($item->from, 8)}}">{{ $item->from }}</td>
                                    <td title="{{number_format($item->to, 8)}}">{{ $item->to }}</td>
                                    {{--备注--}}
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}"
                                           title="{{$item->remark ?: '暂无'}}">查看</a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true" width="auto">
                                            <div class="modal-dialog" role="document" width="auto">
                                                <div class="modal-content" width="auto">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">备注</h5>
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
                                    <td>{{ $item->created_at ?:'--' }}</td>
                                    <td>{{ $item->is_resume ? '已恢复':'--' }}</td>
                                    <td>
                                        @if($item->type == \App\Models\Wallet\WalletsBalanceLog::FROZEN && !$item->is_resume)
                                        <a href="####" onclick="itemUpdate('{{ $item->id }}',
                                                '{{ url("user/walletResume/$item->id") }}?','amount','resume',
                                                '用户资产冻结为<b><strong> 恢复 </strong></b> 状态',
                                                '{{ csrf_token() }}', '恢复冻结');"><i class="fontello-cancel" title="恢复冻结"></i></a>
                                        @else
                                         &nbsp;--
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="14" class="text-center">
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
                                    {{ $balanceLog->appends(Request::except('page'))->links() }}
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
    </script>
@endsection
