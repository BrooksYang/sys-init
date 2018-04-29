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
                        <span>{{ @$editFlag ? '编辑交易用户数字钱包' : '添加交易用户数字钱包' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("user/cryptoWallet/$userCryptoWallet->id") : url('user/cryptoWallet') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{--用户名--}}
                            <div class="col-md-6">
                                <label>用户名</label>
                                <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="username" value="{{ $userCryptoWallet->username ?? old('username') }}"
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
                                        <input class="form-control input-lg" type="text" name="email" value="{{ $userCryptoWallet->email ?? old('email') }}"
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
                                <div class="form-group {{ $errors->has('crypto_wallet_currency_id') ? 'has-error' : '' }}"
                                     style="display: {{ (old('type') || @$permission->group_id) ? 'none' : 'block' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control" name="crypto_wallet_currency_id" required>
                                            <option value="">请选择币种类型</option>
                                            @foreach($currency as $key => $item)
                                                <option value="{{ $item->id }}" {{ (@$userCryptoWallet->crypto_wallet_currency_id == $item->id|| old('crypto_wallet_currency_id') == $item->id) ? 'selected' : '' }}>
                                                    {!! $item->currency_title_cn.'&nbsp;&nbsp;'.$item->currency_title_en_abbr !!}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('crypto_wallet_currency_id'))
                                            <span class="help-block"><strong>{{ $errors->first('crypto_wallet_currency_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- 标题 --}}
                            <div class="col-md-6">
                                <label>交易用户数字钱包名称</label>
                                <div class="form-group {{ $errors->has('crypto_wallet_title') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="crypto_wallet_title" value="{{ $userCryptoWallet->crypto_wallet_title ?? old('crypto_wallet_title') }}"
                                               placeholder="数字钱包名称">
                                        @if ($errors->has('crypto_wallet_title'))
                                            <span class="help-block"><strong>{{ $errors->first('crypto_wallet_title') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 钱包地址 --}}
                            <div class="col-md-6">
                                <label>交易用户数字钱包地址</label>
                                <div class="form-group {{ $errors->has('crypto_wallet_address') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="crypto_wallet_address" value="{{ $userCryptoWallet->crypto_wallet_address ?? old('crypto_wallet_address') }}"
                                               placeholder="数字钱包地址">
                                        @if ($errors->has('crypto_wallet_address'))
                                            <span class="help-block"><strong>{{ $errors->first('crypto_wallet_address') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group {{ $errors->has('crypto_wallet_description') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>钱包描述</label>
                                <textarea class="form-control" name="crypto_wallet_description" rows="5"
                                          placeholder="描述信息">{{ $userCryptoWallet->crypto_wallet_description ?? old('crypto_wallet_description') }}</textarea>
                                @if ($errors->has('crypto_wallet_description'))
                                    <span class="help-block"><strong>{{ $errors->first('crypto_wallet_description') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('user/cryptoWallet') }}" class="btn btn-default">返回</a>
                                <button type="submit" class="btn btn-default pull-right">确定</button>
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
