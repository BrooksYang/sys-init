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
                        <form action="{{ url('issuer/userCurrencyContract') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索币种中文或英文名称" name="search" value="{{ $search ?? Request::get('search')}}">
                            <a href="javascript:;" title="搜索币种中文或英文名称">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        <a href="{{ url('issuer/userCurrencyContract/create') }}" title="添加用户交易合约">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>用户代币交易合约列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>代币名称</th>
                                <th>每日提币上限</th>
                                <th>每日提币次数上限</th>
                                <th>提币手续费率</th>
                                <th>最小充值金额</th>
                                <th>每日卖出限额</th>
                                <th>提醒信息</th>
                                <th>交易对</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            @forelse($userCurrencyContract as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($userCurrencyContract->currentPage() - 1) * $userCurrencyContract->perPage() }}</td>
                                    <td title="{!! $item->currency_title_cn.'&nbsp;&nbsp;('.$item->currency_title_en_abbr.')' !!}"><strong>{!! str_limit($item->currency_title_cn. '('.$item->currency_title_en_abbr.')',15)  !!} </strong></td>
                                    <td>{{ number_format($item->user_withdraw_daily_amount_limit, 8,'.',',')}}</td>
                                    <td>{{ $item->user_withdraw_daily_count_limit }}</td>
                                    <td>{{ $item->user_withdraw_fee_rate }}</td>
                                    <td>{{ number_format($item->user_deposit_minimum_amount, 8,'.',',')}}</td>
                                    <td>{{ number_format($item->user_sell_daily_limit, 8,'.',',')}}</td>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">用户充值/提币提醒信息</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <span><b>充值提醒信息：</b></span>{{ $item->user_deposit_warning ?:'--'}}
                                                        <p></p>
                                                        <span><b>提币提醒信息：</b></span>{{ $item->user_withdraw_warning ?:'--'}}
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLongSymbol{{$key+1}}">
                                            交易对
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLongSymbol{{$key+1}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongSymbol{{$key+1}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <form role="form" method="POST" action="{{ url('userCurrencyContract/symbol/fee') }}" id="symbolFee{{$key+1}}">
                                                    {{ csrf_field() }}
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongSymbolTitle{{$key+1}}">合约交易对信息</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-hover table-striped table-fee">
                                                            <tr>
                                                                <th>交易对</th>
                                                                <th>挂单手续费</th>
                                                                <th>吃单手续费</th>
                                                            </tr>
                                                            @forelse(($symbolByCurrency[$item->currency_id] ?? []) as $key => $symbol)
                                                                <tr>
                                                                    <td class=""><span class="label label-success">{{ $symbol->symbol }}</span></td>
                                                                    <td class="fee"><input type="text" value="{{ $symbol->maker_fee }}" name="symbolFee[{{$symbol->id}}][maker_fee]">
                                                                    @if ($errors->has("symbolFee.$symbol->id.maker_fee"))
                                                                        <span class="help-block" style="color: #a94442"><strong>{{ $errors->first("symbolFee.$symbol->id.maker_fee") }}</strong></span>
                                                                    @endif
                                                                    </td>
                                                                    <td class="fee"><input type="text" value="{{ $symbol->taker_fee }}" name="symbolFee[{{$symbol->id}}][taker_fee]">
                                                                    @if ($errors->has("symbolFee.$symbol->id.taker_fee"))
                                                                        <span class="help-block" style="color: #a94442"><strong>{{ $errors->first("symbolFee.$symbol->id.taker_fee") }}</strong></span><br/>
                                                                    @endif
                                                                    </td>
                                                                </tr>
                                                           @empty
                                                                <tr><td colspan="3" class="text-center">
                                                                        <div class="noDataValue">
                                                                            暂无数据
                                                                        </div>
                                                                    </td></tr>
                                                            @endforelse
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                                        @if($symbolByCurrency[$item->currency_id] ?? '')
                                                            <button type="submit" class="btn btn-secondary" >保存</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                    <td>
                                        <a href="{{ url("issuer/userCurrencyContract/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("issuer/userCurrencyContract/$item->id") }}',
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
                                    {{ $userCurrencyContract->appends(Request::except('page'))->links() }}
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
        if('{{$errors->has('symbolFee.*.maker_fee')}}' || '{{$errors->has('symbolFee.*.taker_fee')}}'){
            layer.msg('交易对费率验证失败');
        }
        if('{{ session('indexMsg') ?? ''}}'){
            layer.msg('更新成功')
        }

        {{--if('{{$errors->has('symbolFee.*.maker_fee')}}'){--}}
          {{--layer.msg('{{ $errors->first('symbolFee.*.maker_fee') }}');--}}
      {{--}else if('{{$errors->has('symbolFee.*.taker_fee')}}'){--}}
          {{--layer.msg('{{ $errors->first('symbolFee.*.taker_fee') }}');--}}
      {{--}--}}
    </script>
@endsection
