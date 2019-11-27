@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统外部地址列表</span>
                    </h3>

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right box-tools">
                        @include('component.conditionSearch', ['url'=>url('otc/sys/withdrawAddr')])

                        {{--添加外部提币地址--}}
                        <!-- Button trigger modal -->
                        <a href="javascript:void(0);" style="color: #fff;" class="btn btn-info" data-toggle="modal" data-target="#exampleModalLong">
                            <i class="fa fa-plus" title="添加外部地址"></i>&nbsp;添加地址
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url('otc/sys/withdrawAddr') }}" method="post" role="form">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">添加外部地址</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label>请选择地址类型</label>
                                                            <select class="flter-status form-control input-sm" id="type" name="type">
                                                                @foreach($type as $key => $item)
                                                                    <option value="{{$key}}" >{{ $item['name'] }} </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('type'))
                                                                <p style="color: red;margin-left: 20px;"><strong>{{ $errors->first('type') }}</strong></p>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label>外部地址</label>
                                                            <input class="form-control input-lg" type="text" name="address" value="{{ old('address') ?? '' }}"
                                                                   placeholder="请填写地址" required>
                                                            @if ($errors->has('address'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('address') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>地址描述</label>
                                                            <input class="form-control input-lg" type="text" name="desc"
                                                                   value="{{ old('desc') ?? '' }}"
                                                                   placeholder="请填写地址描述">
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

                        {{--添加收益科目--}}
                        <!-- Button trigger modal -->
                        <a href="javascript:void(0);" style="color: #fff;margin-left: 10px" class="btn btn-warning"
                           data-toggle="modal" data-target="#exampleModalLongSubject">
                            <i class="fa fa-plus" title="添加科目"></i>&nbsp;添加科目
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalLongSubject" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongSubjectTitle"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url('otc/sys/finance/subject') }}" method="post" role="form">
                                        {{ csrf_field() }}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongSubjectTitle">添加科目</h5>
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
                    {{--多条件搜索--}}
                    <form action="{{ url('otc/sys/withdrawAddr')}}" id="searchForm">
                        <div class="row" style="margin-bottom:10px;">
                            {{--搜索--}}
                            {{--用户基本信息--}}
                            <div class="col-sm-6">
                                <input class="form-control input-sm"  placeholder="请输入用户名或电话或邮箱"
                                       name="search" id="search" type="text" value="{{ Request::get('search')?? '' }}"/>
                            </div>

                            {{--地址类型--}}
                            <div class="col-sm-3">
                                <select class="filter-status form-control input-sm" id="filterType" name="filterType">
                                    <option value="">请选择类型</option>
                                    @foreach($type as $key => $itemType)
                                        <option value="{{$key}}" {{ Request::get('filterType')==$key ? 'selected' :''}}>{{ $itemType['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{--地址状态--}}
                            <div class="col-sm-3">
                                <select class="filter-status form-control input-sm" id="filterStatus" name="filterStatus">
                                    <option value="">请选择地址状态</option>
                                    @foreach($status as $key => $itemStatus)
                                        <option value="{{$key}}" {{ Request::get('filterStatus')==$key ? 'selected' :''}}>
                                            {{ $itemStatus['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户</th>
                                <th>地址</th>
                                <th>累计提币</th>
                                <th>实际到账</th>
                                <th>累计手续费</th>
                                <th>状态</th>
                                <th>描述</th>
                                <th>创建时间
                                    @include('component.sort',['url'=>url('otc/sys/withdrawAddr')])
                                </th>
                                <th>操作</th>
                            </tr>
                            @forelse($external as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($external->currentPage() - 1) * $external->perPage() }}</td>
                                    <td title="UID-{{$item->user_id}} | 电话-{{$item->user->phone ?? '--'}} | 用户名-{{$item->user->username ?? '--' }}">
                                        {{ $item->user_id ? ($item->user->username ?? $item->user->phone) : '系统地址' }}
                                    </td>
                                    <td title="{{ $item->address }}" id="copyAddr{{$key}}" data-attr="{{$item->address}}">
                                        @if($item->address)
                                            @include('component.copy', ['eleId'=>'copyAddr'.$key, 'eleType'=>'attr', 'attr'=>'data-attr'])
                                        @endif
                                        <strong><a {!! $item->address ? 'href="https://etherscan.io/tx/'.$item->address.'"'.' target="_blank"' : '####' !!}>
                                                {{ $item->address ?substr_replace($item->address, '***', 6, 30): '--' }}</a></strong>
                                    </td>
                                    <td title="{{ number_format($item->total,8) }}">{{ floatval($item->total) }}</td>
                                    <td title="{{ number_format($item->amount,8) }}">{{ floatval($item->amount) }}</td>
                                    <td title="{{ number_format($item->amount,8) }}">{{ floatval($item->fee) }}</td>
                                    <td><span class="label label-{{$status[$item->status]['class']}}">{{ $status[$item->status]['name'] }}</span></td>
                                    <td title="{{$item->desc}}">{{ str_limit($item->desc ?: '--', 15) }}</td>
                                    <td>{{ $item->created_at ?:'--' }}</td>
                                    <td>
                                        {{--编辑地址信息--}}
                                        <!-- Button trigger modal -->
                                        @include('component.modalHeader', ['modal'=>'Addr','title'=>'编辑地址','form'=>true, 'methodField'=>'PATCH',
                                                'action'=>url('otc/sys/withdrawAddr').'/'.$item->id, 'icon'=>'fontello-edit',
                                                'headerIcon'=>'fontello-edit','header'=>'编辑地址'])
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label>请选择地址类型</label>
                                                            <select class="flter-status form-control input-sm" id="type" name="type">
                                                                @foreach($type as $key => $itemType)
                                                                    <option value="{{$key}}" {{ $item->type == $key ? 'selected' :'' }}>{{ $type[$item->type]['name'] }} </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('type'))
                                                                <p style="color: red;margin-left: 20px;"><strong>{{ $errors->first('type') }}</strong></p>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label>外部地址</label>
                                                            <input class="form-control input-lg" type="text" name="address" value="{{ $item->address ?? old('address') }}"
                                                                   placeholder="请填写地址" required>
                                                            @if ($errors->has('address'))
                                                                <e class="help-block" style="color: red;"><strong>{{ $errors->first('address') }}</strong></e>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label>地址描述</label>
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

                                        {{--启停用地址--}}
                                        <a href="####" onclick="itemUpdate('{{ $item->id }}',
                                            '{{ url("otc/sys/withdrawAddr/toggle/$item->id") }}','status',true,
                                            '地址<b><strong> {{ $item->status == \App\Models\Wallet\WalletExternal::ENABLE ? '停用' : '启用' }}</strong></b> 状态',
                                            '{{ csrf_token() }}', '{{ $item->status == \App\Models\Wallet\WalletExternal::ENABLE ? '停用' : '启用' }}')">
                                            <i class="fa fa-exchange" title='{{ $item->status == \App\Models\Wallet\WalletExternal::ENABLE ? '停用' : '启用' }}'></i>
                                        </a>

                                        {{--删除地址--}}
                                        <a href="####" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("otc/sys/withdrawAddr/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                @include('component.noData', ['colSpan'=>10])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $external->appends(Request::except('page'))->links() }}
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

            //按钮搜索
            $('#conditionSearch').click(function () {
                var uri = implodeUri();
                $(this).attr('href', uri);
            });

            // 回车搜索
            $("#searchForm").bind("keypress",function(e){
                // 兼容FF和IE和Opera
                var theEvent = e || window.event;
                var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
                if (code == 13) {
                    console.log('{{ Request::url() }}');
                    e.preventDefault();
                    //回车执行查询
                    var uri = implodeUri();
                    window.location.href = '{{ Request::url() }}'+uri;
                }
            });

            // 整理uri searchTeam
            function implodeUri() {
                var uri = '?searchUser='+$('#searchUser').val()
                    +'&searchFromUser='+$('#searchFromUser').val()
                    +'&searchRemark='+$('#searchRemark').val()
                    +'&searchCardNumber='+$('#searchCardNumber').val()
                    +'&searchOtc='+$('#searchOtc').val()
                    +'&searchMerchant='+$('#searchMerchant').val()
                    +'&searchCurrency='+$('#searchCurrency').val()
                    +'&filterType='+$('#filterType').val()
                    +'&filterStatus='+$('#filterStatus').val()
                    +'&filterAppeal='+$('#filterAppeal').val()
                    +'&start='+$('#start').val()
                    +'&end='+$('#end').val();

                return uri;
            }
        });
    </script>
@endsection
