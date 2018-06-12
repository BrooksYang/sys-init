@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('otc/legalCurrency') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索法定币种名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索法定币种名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('otc/legalCurrency/create') }}" title="添加法定币种">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统法定币种类型列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>法币名称</th>
                                <th>法币缩写</th>
                                <th>所属国家/地区</th>
                                <th>国家/地区英文名称</th>
                                <th>修改时间</th>
                                <th>操作</th>
                            </tr>
                            @forelse($legalCurrency as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($legalCurrency->currentPage() - 1) * $legalCurrency->perPage() }}</td>
                                    <td title="{{ $item->name }}"><strong>{{ str_limit($item->name,25) }}</strong></td>
                                    <td><span class="label label-success">{{ strtoupper($item->abbr) }}</span></td>
                                    <td>{{ $item->country ?: '--'}}</td>
                                    <td title="{{ ucwords($item->country_en) }}">{{ str_limit(ucwords($item->country_en),25) ?: '--'}}</td>
                                    <td>{{ $item->created_at ?: '--'}}</td>
                                    <td>
                                        <a href="{{ url("otc/legalCurrency/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/legalCurrency/$item->id") }}',
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
                                    {{ $legalCurrency->appends(Request::except('page'))->links() }}
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
