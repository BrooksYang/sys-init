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
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>币种类型名称</label>
                                <input class="form-control input-lg" type="text" name="title" value="{{ $currencyType->title ?? old('title') }}"
                                       placeholder="币种类型名称">
                                @if ($errors->has('title'))
                                    <span class="help-block"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}

                        <div class="form-group {{ $errors->has('intro') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>币种类型描述</label>
                                <textarea class="form-control" name="intro" rows="5"
                                          placeholder="币种类型描述">{{ $currencyType->intro ?? old('intro') }}</textarea>
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
