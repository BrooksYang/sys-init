@extends('entrance::layouts.default')

@section('css-part')
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <a href="{{ url('issuer/userCurrencyContract/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户代币交易合约列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>代币名称</th>
                                <th>每日提币上限</th>
                                <th>每日提币次数上限</th>
                                <th>提币手续费率</th>
                                <th>最小充值金额</th>
                                <th>每日卖出限额</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            @forelse($userCurrencyContract as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userCurrencyContract->currentPage() - 1) * $userCurrencyContract->perPage() }}</td>
                                    <td title="{!! $item->currency_title_cn.'&nbsp;&nbsp;('.$item->currency_title_en_abbr.')' !!}"><strong>{!! str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15)  !!} </strong></td>
                                    <td>{{ $item->user_withdraw_daily_amount_limit }}</td>
                                    <td>{{ $item->user_withdraw_daily_count_limit }}</td>
                                    <td>{{ $item->user_withdraw_fee_rate }}</td>
                                    <td>{{ $item->user_deposit_minimum_amount }}</td>
                                    <td>{{ $item->user_sell_daily_limit }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        <a href="{{ url("issuer/userCurrencyContract/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("issuer/userCurrencyContract/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <span class="text-enter">暂无数据</span>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $userCurrencyContract->appends(Request::except('page'))->links() }}
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
