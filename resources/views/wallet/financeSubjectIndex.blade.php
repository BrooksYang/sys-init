@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统收益科目列表</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right box-tools">
                        @include('component.searchForm', ['url'=>url('otc/sys/finance/subject'), 'placeholder'=>'搜索科目名称'])

                        {{--添加收益科目--}}
                        <!-- Button trigger modal -->
                        <a href="javascript:void(0);" style="color: #fff;margin-left: 10px" class="btn btn-info"
                           data-toggle="modal" data-target="#exampleModalLong">
                            <i class="fa fa-plus" title="添加外部地址"></i>&nbsp;添加科目
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url('otc/sys/finance/subject') }}" method="post" role="form">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">添加科目</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>科目名称</label>
                                                            <input class="form-control input-md" type="text" name="title" value="{{ old('title') ?? '' }}"
                                                                   placeholder="请填写科目名称" required>
                                                            @if ($errors->has('title'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('title') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>描述</label>
                                                            <input class="form-control input-lg" type="text" name="desc"
                                                                   value="{{ old('desc') ?? '' }}"
                                                                   placeholder="请填写描述">
                                                            @if ($errors->has('desc'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('desc') }}</strong></e>
                                                            @endif
                                                        </div>
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
                        </div>

                        {{--添加提币申请--}}
                        <!-- Button trigger modal -->
                        <a href="javascript:void(0);" style="color: #fff; margin-left: 8px;" class="btn btn-danger"
                           data-toggle="modal" data-target="#exampleModalLongWithdraw">
                            <i class="fontello-export-outline" title="提币申请"></i>&nbsp;提币申请
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalLongWithdraw" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongWithdrawTitle"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url('otc/sys/withdraw') }}" method="post" role="form">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongWithdrawTitle">添加提币申请</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-sm-12">
                                                        <div class="alert alert-info">
                                                            <button data-dismiss="alert" class="close" type="button">×</button>
                                                            {{--<span class="entypo-info-circled"></span>--}}
                                                            <strong>提示：提币限额区间为最小额{{$withdrawMin}}, 最大额{{$withdrawMax}}；</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label>请选择币种类型</label>
                                                            <select class="filter-status form-control input-sm" id="currency_id" name="currency_id">
                                                                <option value="">请选择币种</option>
                                                                @foreach($currencies as $key => $currency)
                                                                    <option value="{{$key}}"
                                                                            {{ $key== \App\Models\Currency::USDT ? 'selected' :''}}>{{ $currency }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('currency_id'))
                                                                <p style="color: red;margin-left: 20px;"><strong>{{ $errors->first('currency_id') }}</strong></p>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label>请选择转入目标地址</label>
                                                            <select class="filter-status form-control input-sm" id="to" name="to" required>
                                                                <option value="">请选择转入目标地址</option>
                                                                @foreach($external as $key => $item)
                                                                    @if($item->status == \App\Models\Wallet\WalletExternal::ENABLE)
                                                                    <option value="{{$item->address }}" {{ old('to') == $item->address ? 'selected' : ''}}
                                                                            title="{{$item->desc}}">{{ $item->address }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('to'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('to') }}</strong></e>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label>请选择科目</label>
                                                            <select class="filter-status form-control input-sm" id="subject_id" name="subject_id" required>
                                                                <option value="">请选择科目</option>
                                                                @foreach($subject as $key => $item)
                                                                    <option value="{{$item->id }}" {{ old('subject_id') == $item->id ? 'selected' : ''}}
                                                                    title="{{$item->title}}">{{ str_limit($item->title, 16) }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('subject_id'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('subject_id') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>填写金额</label>
                                                            <input class="form-control input-lg" type="text" name="amount"
                                                                   value="{{ old('amount') ?? '' }}"
                                                                   placeholder="请填写金额" required>
                                                            @if ($errors->has('amount'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('amount') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>备注</label>
                                                            <input class="form-control input-lg" type="text" name="remark"
                                                                   value="{{ old('remark') ?? '' }}"
                                                                   placeholder="建议填写备注及说明信息" required>
                                                            @if ($errors->has('remark'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('remark') }}</strong></e>
                                                            @endif
                                                        </div>
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
                        </div>
                    </div>
                </div>


                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>标题</th>
                                <th>描述</th>
                                <th>创建时间
                                    @include('component.sort',['url'=>url('otc/sys/finance/subject')])
                                </th>
                                <th>操作</th>
                            </tr>
                            @forelse($subject as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($subject->currentPage() - 1) * $subject->perPage() }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td title="{{$item->desc}}">{{str_limit($item->desc,16) }}</td>
                                    <td>{{ $item->created_at ?:'--' }}</td>
                                    <td>
                                        {{--编辑科目信息--}}
                                        <!-- Button trigger modal -->
                                        @include('component.modalHeader', ['modal'=>'Addr','title'=>'编辑科目','form'=>true, 'methodField'=>'PATCH',
                                                'action'=>url('otc/sys/finance/subject').'/'.$item->id, 'icon'=>'fontello-edit',
                                                'headerIcon'=>'fontello-edit','header'=>'编辑科目'])
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>科目名称</label>
                                                            <input class="form-control input-lg" type="text" name="title" value="{{ $item->title ?? old('title') }}"
                                                                   placeholder="请填写科目名称" required>
                                                            @if ($errors->has('title'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('title') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>描述</label>
                                                            <input class="form-control input-lg" type="text" name="desc"
                                                                   value="{{ $item->desc ?? old('desc') }}"
                                                                   placeholder="请填写地址描述">
                                                            @if ($errors->has('desc'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('desc') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @include('component.modalFooter',['form'=>true])

                                        {{--删除科目--}}
                                        <a href="####" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/sys/finance/subject/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                @include('component.noData', ['colSpan'=>5])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $subject->appends(Request::except('page'))->links() }}
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
           if('{{ $errors->first()}}'){ layer.msg('{{ $errors->first()}}'); }
        });
    </script>
@endsection
