@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">
                        <form action="{{ url('otc/legalCurrency') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索法币名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索法币名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                    {{-- Add--}}
                        <a href="{{ url('otc/legalCurrency/create') }}">
                            <span class="box-btn"><i class="fa fa-plus" title="添加法币"></i></span>
                           {{-- <!-- Button trigger modal -->
                            <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong">
                                <span class="box-btn" title="添加法币汇率"><i class="fa fa-plus"></i></span>
                            </a>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" width="auto">
                                <div class="modal-dialog" role="document" width="auto">
                                    <div class="modal-content" width="auto">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">设置法币汇率（用于价格的多币种显示和汇率核算）</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{url('otc/legalCurrency')}}" method="POST" >
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                <div class="row">
                                                    --}}{{--法币名称--}}{{--
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label>法币名称</label>
                                                                <input class="form-control input-sm" placeholder="请填写法币名称" type="text"  required
                                                                       name="name" value="{{ old('name')  }}">
                                                                @if ($errors->has('name'))
                                                                    <p style="color: red"><strong>{{ $errors->first('name') }}</strong></p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    --}}{{--英文缩写--}}{{--
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label>英文缩写</label>
                                                                <input class="form-control input-sm" placeholder="请填写法币英文缩写" type="text"  required
                                                                       name="abbr" value="{{ old('abbr')  }}">
                                                                @if ($errors->has('abbr'))
                                                                    <p style="color: red"><strong>{{ $errors->first('abbr') }}</strong></p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    --}}{{--货币符号--}}{{--
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label>货币符号</label>
                                                                <input class="form-control input-sm" placeholder="货币符号（如￥，$）" type="text"
                                                                       name="symbol" value="{{ old('symbol') }}">
                                                                @if ($errors->has('symbol'))
                                                                    <p style="color: red"><strong>{{ $errors->first('symbol') }}</strong></p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    --}}{{--法币汇率--}}{{--
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="col-md-12">
                                                                <label>汇率（相对于USDT的单位价值）（即1USDT=***RMB；汇率即为***）</label>
                                                                <input class="form-control input-lg" placeholder="请填写汇率" type="text"  required
                                                                       name="rate" value="{{ old('rate')  }}">
                                                                @if ($errors->has('rate'))
                                                                    <p style="color: red"><strong>{{ $errors->first('rate') }}</strong></p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="radio">
                                                                <label>是否为中文版默认法币：</label>
                                                                <label>
                                                                    <input type="radio" name="is_default_cn" value=1 >{{ $type[1]['name'] }}
                                                                </label>&nbsp;&nbsp;&nbsp;
                                                                <label>
                                                                    <input type="radio" name="is_default_cn" value=2  checked>{{ $type[2]['name'] }}
                                                                </label>
                                                            </div>
                                                            @if ($errors->has('is_default_cn'))
                                                                <p style="color: red;margin-left: 20px;" class="form-group"><strong>{{ $errors->first('is_default_cn') }}</strong></p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="radio">
                                                                <label>是否为英文版默认法币：</label>
                                                                <label>
                                                                    <input type="radio" name="is_default_en" value=1 >{{ $type[1]['name'] }}
                                                                </label>&nbsp;&nbsp;&nbsp;
                                                                <label>
                                                                    <input type="radio" name="is_default_en" value=2  checked>{{ $type[2]['name'] }}
                                                                </label>
                                                            </div>
                                                            @if ($errors->has('is_default_en'))
                                                                <p style="color: red;margin-left: 20px;"><strong>{{ $errors->first('is_default_en') }}</strong></p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                                <button type="submit" class="btn btn-secondary">确定</button>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>--}}
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>法币汇率（用于价格的多币种显示和汇率核算）</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>法币名称</th>
                                <th>英文缩写</th>
                                <th>火币汇率</th>
                                <th>平台买入汇率</th>
                                <th>出售汇率</th>
                                <th>货币符号 </th>
                                <th>默认法币_中文 </th>
                                <th>默认法币_英文 </th>
                                <th>创建时间 &nbsp;&nbsp;<a href="{{ url('otc/legalCurrency')}}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::get('orderC') != 'desc' ? !Request::get('orderC') ? '' : 'gray' :'' }}" title="降序"></i></a> &nbsp;
                                    <a href="{{ url('otc/legalCurrency') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::get('orderC') != 'asc' ? 'gray' : '' }}" title="升序"></i></a>
                                </th>
                                <th>操作</th>
                            </tr>
                            @forelse($legalCurrency as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($legalCurrency->currentPage() - 1) * $legalCurrency->perPage() }}</td>
                                    <td title="{{$item->name}}">{{ str_limit($item->name,15) }}</td>
                                    <td title="{{$item->abbr?:'暂无'}}">{{ str_limit($item->abbr ?:'--',15) }}</td>
                                    <td title="{{number_format($item->rate,8)}}"> {{ floatval($item->rate) }}</td>
                                    <td title="{{number_format($item->rate_buy,8)}}"> {{ floatval($item->rate_buy) }}</td>
                                    <td title="{{number_format($item->rate_sell,8)}}"> {{ floatval($item->rate_sell) }}</td>
                                    <td>{{ $item->symbol ?: '--' }}</td>
                                    <td title="是否为中文版默认法币">
                                        <span class="{{ $item->is_default_cn ==1 ? 'label label-'.$type[$item->is_default_cn]['class']:'' }}">{{ $type[$item->is_default_cn]['name']}}</span>
                                    </td> <td title="是否为英文版默认法币">
                                        <span class="{{ $item->is_default_en ==1 ? 'label label-'.$type[$item->is_default_en]['class']:'' }}">{{ $type[$item->is_default_en]['name']}}</span>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="{{ url("otc/legalCurrency/$item->id/edit") }}"  class="">
                                            <span class="box-btn"><i class="fontello-edit"></i></span>
                                        </a>

                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/legalCurrency/$item->id") }}',
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
        $(function () {
            if('{{$errors->first()}}'){layer.msg('验证失败请重新编辑')}
        });
    </script>
@endsection
