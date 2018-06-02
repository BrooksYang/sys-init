@extends('entrance::layouts.default')

@section('css-import')
@show


@section('js-import')
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑交易用户 OTC 记账钱包' : '添加交易用户 OTC 记账钱包' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("otc/user/wallet/$userOtcWallet->id") : url('otc/user/wallet') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{--用户名--}}
                            <div class="col-md-6">
                                <label>用户名</label>
                                <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="username" value="{{ $userOtcWallet->username ?? old('username') }}"
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
                                        <input class="form-control input-lg" type="text" name="email" value="{{ $userOtcWallet->email ?? old('email') }}"
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
                                <div class="form-group {{ $errors->has('currency_id') ? 'has-error' : '' }}"
                                     style="display: {{ (old('type') || @$permission->group_id) ? 'none' : 'block' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control" name="currency_id" required disabled>
                                            <option value="">请选择币种类型</option>
                                            @foreach($currency as $key => $item)
                                                <option value="{{ $item->id }}" {{ (@$userOtcWallet->currency_id == $item->id|| old('currency_id') == $item->id) ? 'selected' : '' }}>
                                                    {!! $item->currency_title_cn.'&nbsp;&nbsp;'.$item->currency_title_en_abbr !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('currency_id'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- 余额 --}}
                            <div class="col-md-6">
                                <label>记账钱包余额</label>
                                <div class="form-group {{ $errors->has('available') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="available" value="{{ $userOtcWallet->available ?? old('available') }}"
                                               placeholder="记账钱包余额" disabled>
                                        @if ($errors->has('available'))
                                            <span class="help-block"><strong>{{ $errors->first('available') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 冻结金额 --}}
                            <div class="col-md-6">
                                <label>冻结金额</label>
                                <div class="form-group {{ $errors->has('frozen') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="frozen" value="{{ $userOtcWallet->frozen ?? old('frozen') }}"
                                               placeholder="冻结金额" disabled>
                                        @if ($errors->has('frozen'))
                                            <span class="help-block"><strong>{{ $errors->first('frozen') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('otc/user/wallet') }}" class="btn btn-default pull-right">返回</a>
                                {{--<a href="{{ url('otc/user/wallet') }}" class="btn btn-default">返回</a>--}}
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
    </script>
@endsection
