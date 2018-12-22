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
                        <form action="{{ url('portal/token/apply') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索联系人或项目名称" name="search" value="{{ $search }}">
                            <a href="javascript:;" title="搜索联系人或项目名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title">
                        <i class="fontello-doc"></i>
                        <span>上币申请列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>联系人</th>
                                <th>联系方式</th>
                                <th>项目名称</th>
                                <th>项目介绍</th>
                                <th>官方网站</th>
                                <th>合约地址</th>
                                <th>开盘价</th>
                                <th>预算</th>
                                <th>社交地址</th>
                                <th>期望上币时间</th>
                                <th>备注</th>
                                <th>申请时间 &nbsp;&nbsp;<a href="{{ url('portal/token/apply')}}?orderC=desc">
                                            <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                        <a href="{{ url('portal/token/apply') }}?orderC=asc">
                                            <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($application as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($application->currentPage() - 1) * $application->perPage() }}</td>
                                    <td title="{{ $item->contact }}">{{ str_limit($item->contact?:'--',20) }}</td>
                                    <td title="{{ $item->contact_info }}">{{ str_limit($item->contact_info?:'--',25) }}</td>
                                    <td title="{{ $item->project }}">{{ str_limit($item->project?:'--',25) }}</td>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">项目介绍</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body {{ $item->intro?'':'text_c' }}">
                                                        {!! $item->intro?:'暂无数据' !!}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td title="{{ $item->website }}"><a href="{{$item->website?:'javascript:void(0);'}}" target="_blank">{{ str_limit($item->website?:'--',20) }}</a></td>
                                    <td title="{{ $item->token_addr }}"><a href="{{$item->token_addr?:'javascript:void(0);'}}" target="_blank">{{ str_limit($item->token_addr?:'--',20) }}</a></td>
                                    <td title="{{ number_format($item->opening_price,5) }}">{{ number_format($item->opening_price,5) }}</td>
                                    <td title="{{ number_format($item->budget,5) }}">{{ number_format($item->budget,5) }}</td>
                                    <td title="{{ $item->social_link }}">{{ str_limit($item->social_link?:'--',20) }}</td>
                                    <td title="{{ $item->expected_at ?:'--' }}">{{ $item->expected_at ?:'--' }}</td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLongRemark{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLongRemark{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongRemarkTitle{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongRemarkTitle{{$key}}">备注</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body {{ $item->remark?'':'text_c' }}">
                                                        {!! $item->remark?:'暂无数据' !!}
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
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("portal/token/apply/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="14" class="text-center">
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
                                    {{ $application->appends(Request::except('page'))->links() }}
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
