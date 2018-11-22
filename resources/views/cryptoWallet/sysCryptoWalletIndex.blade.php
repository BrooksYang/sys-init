@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('sys/cryptoWallet') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索钱包或币种名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索钱包或币种名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('sys/cryptoWallet/create') }}" title="添加系统数字钱包">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>

                        {{--按钱包类型筛选--}}
                        <div style="display: inline-block;position: relative">
                            <a data-toggle="dropdown" class="dropdown-toggle"  type="button" title="按类型筛选钱包">
                                <span class="box-btn"><i class="fa fa-bars" title="按类型筛选钱包"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu">
                                @foreach($type as $key=>$item)
                                    <li>
                                        <a href="{{ url('sys/cryptoWallet') }}?filterType={{$key}}">{{$item['name']}}
                                            {!!  (Request::get('filterType') == $key) || (!Request::get('filterType') && $key == 0)? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统运营平台数字钱包列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>钱包名称</th>
                                <th>钱包地址</th>
                                <th>币种</th>
                                <th title="普通/主钱包">钱包类型&nbsp;<i class="fa fa-info-circle"></i></th>
                                <th>描述</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            @forelse($sysCryptoWallet as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($sysCryptoWallet->currentPage() - 1) * $sysCryptoWallet->perPage() }}</td>
                                    <td title="{{$item->sys_crypto_wallet_title}}">{{ str_limit($item->sys_crypto_wallet_title,15) }}</td>
                                    <td title="{{$item->sys_crypto_wallet_address}}">{{ str_limit($item->sys_crypto_wallet_address,15) }}</td>
                                    <td title="{{$item->currency_title_cn.' ('.$item->currency_title_en_abbr.')'}}">
                                        <span class="label label-success">{{ str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15) }} </span>
                                    </td>
                                    <td title="{{ @$type[$item->type]['name'] }}">{{ @$type[$item->type]['name'] }}</td>
                                    <td>
                                     <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{{$item->sys_crypto_wallet_title}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body {{ $item->sys_crypto_wallet_description ? '' : 'text_c' }}">
                                                        {{$item->sys_crypto_wallet_description ?: '暂无数据'}}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->created_at ? $item->created_at : '--' }}</td>
                                    <td>
                                        <a href="{{ url("sys/cryptoWallet/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("sys/cryptoWallet/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">
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
                                    {{ $sysCryptoWallet->appends(Request::except('page'))->links() }}
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
