@extends('entrance::layouts.default')

@section('css-part')
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    {{--<div class="pull-right box-tools">
                        <a href="{{ url('sys/wallet/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>--}}

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统记账钱包列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>币种</th>
                                <th>余额</th>
                                <th>冻结金额</th>
                                <th>操作</th>
                            </tr>
                            @forelse($sysWallet as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($sysWallet->currentPage() - 1) * $sysWallet->perPage() }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',25) }}</span>
                                    </td>
                                    <td title="{{$item->sys_wallet_balance}}">{{ $item->sys_wallet_balance }}</td>
                                    <td title="{{$item->sys_wallet_balance_freeze_amount}}">{{ $item->sys_wallet_balance_freeze_amount }}</td>
                                    <td>
                                        <a href="{{ url("sys/wallet/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("sys/wallet/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <span class="text-center">暂无数据</span>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $sysWallet->appends(Request::except('page'))->links() }}
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
