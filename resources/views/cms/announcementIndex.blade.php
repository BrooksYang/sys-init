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
                        <form action="{{ url('cms/announcement') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索公告标题或创建人" name="search" value="{{ $search }}">
                            <a href="javascript:;" title="搜索公告标题或创建人">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('cms/announcement/create') }}" title="添加公告">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选公告">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选公告"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('cms/announcement') }}">全部
                                    {!! in_array( Request::get('filter'),[1,2]) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                </a>
                            </li>
                            @foreach($announcementStatus as $key=>$item)
                                <li>
                                    <a href="{{ url('cms/announcement') }}?filter={{$key}}">{{$item['name']}}
                                        {!!  Request::get('filter') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统公告列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>公告标题</th>
                                <th>摘要</th>
                                <th>状态</th>
                                <th>创建人</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('cms/announcement')}}?orderC=desc">
                                            <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                        <a href="{{ url('cms/announcement') }}?orderC=asc">
                                            <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($announcement as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($announcement->currentPage() - 1) * $announcement->perPage() }}</td>
                                    <td title="{{ $item->anno_title }}"><strong>{!!$item->anno_top == 1 ? '<i class="fontello-flag-filled" title="已置顶"></i>' : '' !!}
                                            <a href="{{ url("cms/announcement/$item->id")}}">{{ str_limit($item->anno_title,30) }} </a></strong>
                                    </td>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{{str_limit($item->anno_title,35)}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body {{ $item->anno_summary ? '' : 'text_c' }}">
                                                        {{$item->anno_summary ?: '暂无数据'}}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="label label-{{ $item->anno_draft == 1 ? 'default' : 'success' }}">{{ $item->anno_draft == 1 ? '草稿' : '发布'  }}</span>
                                    </td>
                                    <td title="{{ $item->email }}">{{ str_limit($item->name, 15) }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    {{--<td>{{ $item->updated_at ?? '--' }}</td>--}}
                                    <td>
                                        @if($item->anno_draft == 2 && $item->anno_top ==2)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("cms/announcement/updateStatus/$item->id") }}?anno_draft=2','anno_top',1,
                                                    '公告为<b><strong> 置顶 </strong></b> 状态',
                                                    '{{ csrf_token() }}','置顶');"> <i class="fa fa-hand-o-up" title="置顶"></i></a>
                                        @elseif($item->anno_draft == 2 &&$item->anno_top == 1)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("cms/announcement/updateStatus/$item->id") }}?anno_draft=2','anno_top',2,
                                                    '公告为<b><strong> 取消置顶 </strong></b> 状态',
                                                    '{{ csrf_token() }}','取消置顶');"> <i class="fa fa-hand-o-down" title="取消置顶"></i></a>
                                        @endif
                                        @if($item->anno_draft == 1)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("cms/announcement/updateStatus/$item->id") }}?anno_top=2','anno_draft',2,
                                                    '公告为<b><strong> 发布 </strong></b> 状态',
                                                    '{{ csrf_token() }}','发布');"> <i class="fontello-volume-high" title="发布"></i></a>
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("cms/announcement/updateStatus/$item->id") }}?anno_top=1','anno_draft',2,
                                                    '公告为<b><strong> 发布并置顶 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '发布并置顶');"> <i class="fontello-export" title="发布并置顶"></i></a>
                                        @elseif($item->anno_draft == 2)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("cms/announcement/updateStatus/$item->id") }}?anno_top=2','anno_draft',1,
                                                    '公告为<b><strong> 草稿 </strong></b> 状态',
                                                    '{{ csrf_token() }}', '草稿');">&nbsp; <i class="fontello-volume-off" title="设为草稿"></i></a>
                                        @endif
                                        <a href="{{ url("cms/announcement/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("cms/announcement/$item->id") }}',
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
                                    {{ $announcement->appends(Request::except('page'))->links() }}
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
