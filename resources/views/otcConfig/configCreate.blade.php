@extends('entrance::layouts.default')

@section('css-import')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/entrance/js/range-slider/juery.range2dslider.css') }}" />
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <i class="fontello-doc"></i>
                        <span>{{  @$editFlag ? '编辑普通交易配置' : '普通交易配置' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("otc/config/1") : url('otc/config') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        @forelse($configs as $item)
                            {{-- 配置项 --}}
                            <div class="row">
                                @foreach($item as $key => $config)
                                    <div class="col-md-{{ count($item)==1 ? 12 : in_array('about_us', array_column($item,'key')) ? 12 :6 }}">
                                        <div class="form-group {{ $errors->has($config->key)  ? 'has-error' : '' }}">
                                            <div class="col-sm-12">
                                                <label title="" >
                                                    {{  $config->title }}
                                                </label>&nbsp;
                                                @if($config->key == 'about_us')
                                                    <textarea class="form-control" name="{{ $config->key }}" rows="5"
                                                              placeholder="{{  $config->title }}" required>{{  old($config->key) ?? $config->value }}</textarea>
                                                @else
                                                    <input class="form-control input-sm" type="text" name="{{ $config->key }}" value="{{ old($config->key) ?? $config->value }}"
                                                           placeholder="{{  $config->title }}" required>
                                                @endif
                                                @if ($errors->has($config->key))
                                                    <span class="help-block"><strong>{{ $errors->first($config->key) }}</strong></span>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($loop -> last)
                                {{-- Buttons --}}
                                <p style="margin-bottom: 50px"></p>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        {{--<a href="{{ url('otc/config') }}" class="btn btn-default">返回</a>--}}
                                        <button type="submit" class="btn btn-default pull-right">确定</button>
                                    </div>
                                </div>
                            @endif

                        @empty
                            <div class="noDataValue">
                                暂无数据
                            </div>
                        @endforelse
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
        $(function () {

        })
    </script>
@endsection
