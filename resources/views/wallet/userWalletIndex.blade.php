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
                        <a href="{{ url('user/wallet/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>--}}

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>交易用户记账钱包列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名称</th>
                                <th>邮箱账号</th>
                                <th>币种</th>
                                <th>余额</th>
                                <th>冻结金额</th>
                                <th>操作</th>
                            </tr>
                            @forelse($userWallet as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userWallet->currentPage() - 1) * $userWallet->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->email}}">{{ str_limit($item->email,15) }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{$item->user_wallet_balance}}">{{ $item->user_wallet_balance }}</td>
                                    <td title="{{$item->user_wallet_balance_freeze_amount}}">{{ $item->user_wallet_balance_freeze_amount }}</td>
                                    <td>
                                        <a href="{{ url("user/wallet/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("user/wallet/$item->id") }}',
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
                                    {{ $userWallet->appends(Request::except('page'))->links() }}
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
