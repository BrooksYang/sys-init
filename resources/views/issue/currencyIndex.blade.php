@extends('entrance::layouts.default')

@section('css-part')
    @parent
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('issuer/currencyTypeInit') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索币种或类型名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索币种或类型名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('issuer/currencyTypeInit/create') }}" title="添加系统发行币种">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统发行币种列表</span>
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
                                <th>币种类型</th>
                                <th>币种图标</th>
                                <th>发行数量</th>
                                <th>流通数量</th>
                                <th>简介</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            @forelse($currency as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($currency->currentPage() - 1) * $currency->perPage() }}</td>
                                    <td title="{{ $item->currency_title_cn }}"><strong>{{ str_limit($item->currency_title_cn,15) }}</strong></td>
                                    <td title="{{$item->currency_title_en}}">{{ str_limit($item->currency_title_en,15) }}</td>
                                    <td> <span class="label label-success">{{ $item->currency_title_en_abbr }}</span></td>
                                    <td title="{{$item->title}}">{{ str_limit($item->title,15) }}</td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLongIcon{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLongIcon{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongIconTitle{{$key}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongIconTitle{{$key}}">币种图标</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div style="text-align: center"><img src="{{url('currencyIcon')}}/{{ $item->currency_icon }}" style="width:150px"
                                                                onerror="this.src='http://placehold.it/150x150'"/></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->currency_issue_amount }}</td>
                                    <td>{{ $item->currency_issue_circulation }}</td>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{{$item->currency_title_cn}}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><b>发币方官网地址：</b>{{$item->currency_issuer_website}}</p>
                                                        <p><b>白皮书地址：</b>{{$item->white_paper_url}}</p>
                                                        <p><b>钱包下载地址：</b>{{$item->wallet_download_url}}</p>
                                                        <p><b>区块查询链接：</b>{{$item->block_chain_record_url}}</p>
                                                        <b>币种简介：</b>
                                                        {{$item->currency_intro}}
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
                                        <a href="{{ url("issuer/currencyTypeInit/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("issuer/currencyTypeInit/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="11" class="text-center">
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
                                    {{ $currency->appends(Request::except('page'))->links() }}
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
