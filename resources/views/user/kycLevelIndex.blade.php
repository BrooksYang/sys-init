@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('user/kycLevel/manage') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索认证等级名称" name="search"
                                   value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索认证等级名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                       {{-- <a href="{{ url('user/kycLevel/manage/create') }}" title="添加KYC认证等级">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统 KYC 认证等级列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>等级名称</th>
                                <th>等级值</th>
                                {{--<th>单日可提币上限</th>--}}
                                <th>描述</th>
                                <th>更新时间</th>
                                {{--<th>操作</th>--}}
                            </tr>
                            @forelse($kycLevel as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($kycLevel->currentPage() - 1) * $kycLevel->perPage() }}</td>
                                    <td title="{{ $item->name }}"><strong>{{ str_limit($item->name,15) }}</strong></td>
                                    <td title="{{ $item->level }}"><strong>{{ $item->level }}</strong></td>
                                    {{--<td>{{ number_format($item->withdraw_amount_daily,8) }}</td>--}}
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLongDesc{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLongDesc{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongDescTitle{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongDescTitle{{$key}}">等级描述</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body {{ !$item->description ? '暂无数据' :'' }}">
                                                       {{ $item->description ?:'暂无数据' }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->created_at ? $item->created_at : '--' }}</td>
                                    {{--<td>
                                        <a href="{{ url("user/kycLevel/manage/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("user/kycLevel/manage/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>--}}
                                </tr>
                            @empty
                                @include('component.noData', ['colSpan' => 5])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $kycLevel->appends(Request::except('page'))->links() }}
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
