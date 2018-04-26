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
                                <th>简介</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            @foreach($issuers as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($issuers->currentPage() - 1) * $issuers->perPage() }}</td>
                                    <td title="{{ $item->name_cn }}"><strong>{{ str_limit($item->name_cn,15) }}</strong></td>
                                    <td>{{ $item->name_en }}</td>
                                    <td> <span class="label label-success">{{ $item->abbr_en }}</span></td>
                                    <td>{{ $item->issuer }}</td>
                                    <td title="{{ $item->addr }}">{{ str_limit($item->addr,15) }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>
                                     <!-- Button trigger modal -->
                                        <a href="javacrip:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{{$item->name_cn}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{$item->intro}}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->created_at }}</td>
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
                            @endforeach
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
