@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <i class="fontello-doc"></i>
                        <span>{{ @$editFlag ? '编辑交易用户记账钱包' : '添加交易用户记账钱包' }}</span>
                    </h3>

                    <div class="pull-right box-tools">
                        <!-- Button trigger modal -->
                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalEdit" title="编辑用户记账钱包">
                            <i class="fontello-edit"></i> 编辑
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalEditTitle"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url('user/wallet').'/'.$userWallet->id }}" role="form" method="POST" >
                                            {{ csrf_field() }}
                                            {{  method_field('PATCH')}}
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalEditTitle"><i class="fontello-edit"></i>编辑用户记账钱包</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="col-md-6">
                                                            <label>操作</label>
                                                            <div class="radio">
                                                                <label >
                                                                    <input type="radio" name="action" value='add' checked>增加
                                                                </label>&nbsp;&nbsp;&nbsp;
                                                                <label >
                                                                    <input type="radio" name="action" value='sub'>扣除
                                                                    @if ($errors->has('action'))
                                                                        <p class="help-block" style="color: red;"><strong>{{ $errors->first('action') }}</strong></p>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>余额</label>
                                                            <div class="radio">
                                                                <label >
                                                                    <input type="radio" name="balance" value='available' checked>可用余额
                                                                </label>&nbsp;&nbsp;&nbsp;
                                                                <label >
                                                                    @if ($errors->has('balance'))
                                                                        <p class="help-block" style="color: red;"><strong>{{ $errors->first('balance') }}</strong></p>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <br>
                                                            <label>填写修改数额</label>
                                                            <input class="form-control input-lg" type="text" name="amount"
                                                                   value="{{ $item->amount ?? old('amount') }}"  placeholder="请填写数额">
                                                            @if ($errors->has('amount'))
                                                                <p class="" style="color: red;"><strong>{{ $errors->first('amount') }}</strong></p>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-12">
                                                            <br>
                                                            <label>备注信息</label>
                                                            <input class="form-control input-lg" type="text" name="remark"
                                                                   value="{{ $item->remark ?? old('remark') }}"  placeholder="建议填写备注或说明">
                                                            @if ($errors->has('remark'))
                                                                <p class="" style="color: red;"><strong>{{ $errors->first('remark') }}</strong></p>
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
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("user/wallet/$userWallet->id") : url('user/wallet') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{--用户名--}}
                            <div class="col-md-6">
                                <label>用户名</label>
                                <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="username" value="{{ $userWallet->username ?? old('username') }}"
                                               placeholder="用户名" disabled>
                                        @if ($errors->has('username'))
                                            <span class="help-block"><strong>{{ $errors->first('username') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--用户邮箱账号--}}
                            <div class="col-md-6">`
                                <label>用户邮箱账号</label>
                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="email" value="{{ $userWallet->email ?? old('email') }}"
                                               placeholder="用户邮箱账号" disabled>
                                        @if ($errors->has('email'))
                                            <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            {{--选择币种类型--}}
                            <div class="col-md-12">
                                <label>币种类型</label>
                                <div class="form-group {{ $errors->has('user_wallet_currency_id') ? 'has-error' : '' }}"
                                     style="display: {{ (old('type') || @$permission->group_id) ? 'none' : 'block' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control" name="user_wallet_currency_id" required disabled>
                                            <option value="">请选择币种类型</option>
                                            @foreach($currency as $key => $item)
                                                <option value="{{ $item->id }}" {{ (@$userWallet->user_wallet_currency_id == $item->id|| old('user_wallet_currency_id') == $item->id) ? 'selected' : '' }}>
                                                    {!! $item->currency_title_cn.'&nbsp;&nbsp;'.$item->currency_title_en_abbr !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('user_wallet_currency_id'))
                                            <span class="help-block"><strong>{{ $errors->first('user_wallet_currency_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- 余额 --}}
                            <div class="col-md-6">
                                <label>记账钱包余额</label>
                                <div class="form-group {{ $errors->has('user_wallet_balance') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="user_wallet_balance" value="{{ $userWallet->user_wallet_balance ?? old('user_wallet_balance') }}"
                                               placeholder="记账钱包余额" disabled>
                                        @if ($errors->has('user_wallet_balance'))
                                            <span class="help-block"><strong>{{ $errors->first('user_wallet_balance') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 冻结金额 --}}
                            <div class="col-md-6">
                                <label>冻结金额</label>
                                <div class="form-group {{ $errors->has('user_wallet_balance_freeze_amount') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="user_wallet_balance_freeze_amount" value="{{ $userWallet->user_wallet_balance_freeze_amount ?? old('user_wallet_balance_freeze_amount') }}"
                                               placeholder="冻结金额" disabled>
                                        @if ($errors->has('user_wallet_balance_freeze_amount'))
                                            <span class="help-block"><strong>{{ $errors->first('user_wallet_balance_freeze_amount') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('user/wallet') }}" class="btn btn-default pull-right">返回</a>
                                {{--<a href="{{ url('user/wallet') }}" class="btn btn-default">返回</a>--}}
                                {{--<button type="submit" class="btn btn-default pull-right">确定</button>--}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
        if('{{$errors->first()}}'){ layer.msg('验证失败') }
    </script>
@endsection
