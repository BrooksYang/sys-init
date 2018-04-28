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
                        <a href="{{ url('issuer/issurerInit/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统发币方列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>中文全称</th>
                                <th>英文全称</th>
                                <th>英文简称</th>
                                <th>账号</th>
                                <th>地址</th>
                                <th>联系电话</th>
                                <th>创建时间</th>
                                {{--<th>修改时间</th>--}}
                                <th>操作</th>
                            </tr>
                            @forelse($issuers as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($issuers->currentPage() - 1) * $issuers->perPage() }}</td>
                                    <td title="{{ $item->issuer_title_cn }}"><strong>{{ str_limit($item->issuer_title_cn,15) }}</strong></td>
                                    <td title="{{$item->issuer_title_en}}">{{ str_limit($item->issuer_title_en,15) }}</td>
                                    <td> <span class="label label-success">{{ $item->issuer_title_en_abbr }}</span></td>
                                    <td title="{{$item->issuer_account}}">{{ str_limit($item->issuer_account,15) }}</td>
                                    <td title="{{ $item->issuer_address }}">{{ str_limit($item->issuer_address,15) }}</td>
                                    <td>{{ $item->issuer_phone }}</td>
                                    <td>{{ $item->created_at }}</td>
                                   {{-- <td>{{ $item->updated_at ? $item->updated_at: '--'  }}</td>--}}
                                    <td>
                                        <a href="{{ url("issuer/issurerInit/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("issuer/issurerInit/$item->id") }}',
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
                                    {{ $issuers->appends(Request::except('page'))->links() }}
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
