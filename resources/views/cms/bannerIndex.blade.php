@extends('entrance::layouts.default')

@section('css-part')
    @parent
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('portal/ads') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索标题" name="search" value="{{ $search }}">
                            <a href="javascript:;" title="搜索标题">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('portal/ads/create') }}" title="添加轮播">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统轮播图列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>轮播标题</th>
                                <th>URL</th>
                                <th>排序号</th>
                                <th>轮播图</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('portal/ads')}}?orderC=desc">
                                            <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                        <a href="{{ url('portal/ads') }}?orderC=asc">
                                            <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($banner as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($banner->currentPage() - 1) * $banner->perPage() }}</td>
                                    <td title="{{ $item->title }}">{{ str_limit($item->title?:'--',30) }}</td>
                                    <td title="{{ $item->url }}">{{ str_limit($item->url?:'--',30) }}</td>
                                    <td title="{{ $item->order }}">{{ $item->order?:'--' }}</td>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">系统Banne图&nbsp; {{ str_limit($item->title,15) }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text_c">
                                                        <div style="text-align: center;">
                                                            <img src="{{url($item->cover)}}" style="width:{{ config('imgCrop.banner.preview.crop_width') }}px"
                                                             onerror="this.src='http://placehold.it/{{ config('imgCrop.banner.preview.crop_width') }}x{{ config('imgCrop.banner.preview.crop_height') }}'"/></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->created_at }}</td>
                                    {{--<td>{{ $item->updated_at ?? '--' }}</td>--}}
                                    <td>
                                        <a href="{{ url("portal/ads/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("portal/ads/$item->id") }}',
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
                                    {{ $banner->appends(Request::except('page'))->links() }}
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
