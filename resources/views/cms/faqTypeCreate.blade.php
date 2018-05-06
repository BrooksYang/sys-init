@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑系统FAQ类型' : '添加系统FAQ' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("faq/type/$faqType->id") : url('faq/type') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('type_title') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>FAQ 类型名称</label>
                                <input class="form-control input-lg" type="text" name="type_title" value="{{ $faqType->type_title ?? old('type_title') }}"
                                       placeholder="请填写 FAQ 类型名称">
                                @if ($errors->has('type_title'))
                                    <span class="help-block"><strong>{{ $errors->first('type_title') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}

                        <div class="form-group {{ $errors->has('type_description') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>FAQ 类型描述</label>
                                <textarea class="form-control" name="type_description" rows="5"
                                          placeholder="请填写 FAQ 类型描述">{{ $faqType->type_description ?? old('type_description') }}</textarea>
                                @if ($errors->has('type_description'))
                                    <span class="help-block"><strong>{{ $errors->first('type_description') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('faq/type') }}" class="btn btn-default">返回</a>
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
