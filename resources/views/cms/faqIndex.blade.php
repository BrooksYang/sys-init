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
                        <form action="{{ url('faq/manage') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索关键词或标题" name="search" value="{{ Request::get('search')?? '' }}">
                            <a href="javascript:;" title="搜索关键词或标题">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('faq/manage/create') }}" title="添加文档">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                        {{--筛选类别--}}
                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按类别筛选文档">
                            <span class="box-btn"><i class="fontello-menu" title="按类别筛选文档"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('faq/manage') }}">全部
                                    {!! Request::getRequestUri() == '/faq/manage' ? '&nbsp;<i class="fa fa-check txt-info"></i>' :''!!}
                                </a>
                            </li>
                            @foreach($faqType as $key=>$item)
                                <li>
                                    <a href="{{ url('faq/manage') }}?filterType={{$item->id}}">{{$item->type_title}}
                                        {!!  Request::get('filterType') == $item->id ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        {{--筛选状态--}}
                    {{--    <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按状态筛选文档">
                            <span class="box-btn"><i class="fa fa-filter" title="按状态筛选文档"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('faq/manage') }}">全部
                                    {!! in_array( Request::get('filterStatus'),array_keys($faqStatus)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
                                </a>
                            </li>
                            @foreach($faqStatus as $key=>$item)
                                <li>
                                    <a href="{{ url('faq/manage') }}?filterStatus={{$key}}">{{$item['name']}}
                                        {!!  Request::get('filterStatus') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>--}}
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统文档列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>文档标题</th>
                                <th>关键词</th>
                                <th>状态</th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('faq/manage')}}?orderC=desc">
                                            <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != '=desc' ? !Request::get('orderC') ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                        <a href="{{ url('faq/manage') }}?orderC=asc">
                                            <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != '=asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($faq as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($faq->currentPage() - 1) * $faq->perPage() }}</td>
                                    <td title="{{ $item->faq_title }}"><strong>
                                            <a href="{{ url("faq/manage/$item->id")}}">{{ str_limit($item->faq_title,30) }} </a></strong>
                                    </td>
                                    <td title="{{ $item->faq_key_words}}"><span class="label label-info">{{ str_limit($item->faq_key_words,15) }}</span></td>
                                    <td>
                                        <span class="label label-{{ $faqStatus[$item->is_draft]['class'] }}">{{ $faqStatus[$item->is_draft]['name'] }}</span>
                                    </td>

                                    <td>{{ $item->created_at ?: '--'}}</td>
                                    {{--<td>{{ $item->updated_at ?? '--' }}</td>--}}
                                    <td>
                                        @if($item->is_draft == 2)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("faq/manage/updateStatus/$item->id") }}','is_draft',1,
                                                    '文档为<b><strong> 草稿 </strong></b> 状态',
                                                    '{{ csrf_token() }}');"> <i class="fontello-volume-off" title="设为草稿"></i></a>
                                        @else
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                    '{{ url("faq/manage/updateStatus/$item->id") }}?','is_draft',2,
                                                    '文档为<b><strong> 发布 </strong></b> 状态',
                                                    '{{ csrf_token() }}');"> <i class="fontello-volume-high" title="发布"></i></a>
                                        @endif
                                        <a href="{{ url("faq/manage/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("faq/manage/$item->id") }}',
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
                                    {{ $faq->appends(Request::except('page'))->links() }}
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
