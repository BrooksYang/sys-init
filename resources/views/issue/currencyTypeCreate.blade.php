@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑币种类型' : '初始化币种类型' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("issuer/currencyTypeMg/$currencyType->id") : url('issuer/currencyTypeMg') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <input class="form-control input-lg" type="text" name="name" value="{{ $currencyType->name ?? old('name') }}"
                                       placeholder="币种类型名称">
                                @if ($errors->has('name'))
                                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group {{ $errors->has('intro') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <textarea class="form-control" name="intro" rows="5"
                                          placeholder="币种描述">{{ $currencyType->intro ?? old('intro') }}</textarea>
                                @if ($errors->has('intro'))
                                    <span class="help-block"><strong>{{ $errors->first('intro') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('issuer/currencyTypeMg') }}" class="btn btn-default">返回</a>
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
