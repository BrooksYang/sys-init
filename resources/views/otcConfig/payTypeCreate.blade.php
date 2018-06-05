@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑 OTC 订单支付类型' : '初始化 OTC 订单支付类型' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("otc/payType/$currencyType->id") : url('otc/payType') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>支付类型名称</label>
                                <input class="form-control input-lg" type="text" name="name" value="{{ $currencyType->name ?? old('name') }}"
                                       placeholder="OTC 订单支付类型名称">
                                @if ($errors->has('name'))
                                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Name_en --}}
                        <div class="form-group {{ $errors->has('name_en') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>支付类型英文名称</label>
                                <input class="form-control input-lg" type="text" name="name_en" value="{{ $currencyType->name_en ?? old('name_en') }}"
                                       placeholder="OTC 订单支付类型英文名称">
                                @if ($errors->has('name_en'))
                                    <span class="help-block"><strong>{{ $errors->first('name_en') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- icon --}}
                        <div class="form-group {{ $errors->has('icon') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>支付类型图标</label>
                                <input class="form-control input-lg" type="text" name="icon" value="{{ $currencyType->icon ?? old('icon') }}"
                                       placeholder="OTC 订单支付类型图标(请使用类似fontawsome图标)">
                                @if ($errors->has('icon'))
                                    <span class="help-block"><strong>{{ $errors->first('icon') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('otc/payType') }}" class="btn btn-default">返回</a>
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
