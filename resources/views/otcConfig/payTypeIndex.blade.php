@extends('entrance::layouts.default')

@section('css-part')
    @parent
    <link href="{{ asset('/css/hbfont/hbfont.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .hbfont [class^="icon-"]:before,
        .hbfont [class*=" icon-"]:before{
            font-family: inherit;
        }
    </style>
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('otc/payType') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索支付类型名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索支付类型名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('otc/payType/create') }}" title="添加支付类型">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统 OTC 订单支付类型列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>支付类型名称</th>
                                <th>英文名称</th>
                                <th>Icon 图标</th>
                                <th>修改时间</th>
                                <th>操作</th>
                            </tr>
                            @forelse($payType as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($payType->currentPage() - 1) * $payType->perPage() }}</td>
                                    <td title="{{ $item->name }}"><strong>{{ str_limit($item->name,25) }}</strong></td>
                                    <td>{{ ucwords($item->name_en) ?: '--'}}</td>
                                    <td class="hbfont"><i class="{{ $item->icon ?: 'fa fa-credit-card' }}" ></i></td>
                                    <td>{{ $item->updated_at ?: '--'}}</td>
                                    <td>
                                        <a href="{{ url("otc/payType/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/payType/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">
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
                                    {{ $payType->appends(Request::except('page'))->links() }}
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
