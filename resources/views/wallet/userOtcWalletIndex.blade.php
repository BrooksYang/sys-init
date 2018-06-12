@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('otc/user/wallet') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索用户名或币种名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索用户名或币种名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                       {{-- <a href="{{ url('otc/user/wallet/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>交易用户 OTC 记账钱包列表</span>
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
                            @forelse($userOtcWallet as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userOtcWallet->currentPage() - 1) * $userOtcWallet->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->email}}">{{ str_limit($item->email,15) }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }}</span>
                                    </td>
                                    <td title="{{number_format($item->available,8,'.',',')}}">{{ number_format($item->available,8,'.',',')}}</td>
                                    <td title="{{number_format($item->frozen,8,'.',',')}}">{{ number_format($item->frozen,8,'.',',') }}</td>
                                    <td>
                                        <a href="{{ url("otc/user/wallet/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/user/wallet/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
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
                                <div class="pull-right">
                                    {{ $userOtcWallet->appends(Request::except('page'))->links() }}
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
