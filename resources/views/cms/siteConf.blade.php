@extends('entrance::layouts.default')

@section('css-import')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <i class="fontello-doc"></i>
                        <span>{{ @$editFlag ? '编辑-站点说明及条款配置信息' : '站点说明及条款信息配置' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("portal/siteConf/1") : url('portal/siteConf') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        @forelse($configs as $key => $config)
                                {{-- 配置项 --}}
                                @if($key == 'currency_intro')
                                <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label title="" >币种介绍</label>&nbsp;
                                        <textarea id="questionContent_{{$key}}" name="{{ $key }}" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="币种介绍">{{ $config ?? old($key) }}</textarea>
                                        @if ($errors->has($key))
                                            <span class="help-block"><strong>{{ $errors->first($key) }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($key == 'privacy_policy')
                                <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label title="" >隐私条款</label>
                                        <textarea id="questionContent_{{$key}}" name="{{ $key }}" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="隐私条款">{{ $config ?? old($key) }}</textarea>
                                        @if ($errors->has($key))
                                            <span class="help-block"><strong>{{ $errors->first($key) }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($key == 'token_apply_intro')
                                <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label title="" >上下币说明</label>
                                        <textarea id="questionContent_{{$key}}" name="{{ $key }}" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="上下币说明">{{ $config ?? old($key) }}</textarea>
                                        @if ($errors->has($key))
                                            <span class="help-block"><strong>{{ $errors->first($key) }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($key == 'disclaimer')
                                <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label title="" >免责声明</label>
                                        <textarea id="questionContent_{{$key}}" name="{{ $key }}" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="{{ $key == 'disclaimer' ? '免责声明':'' }}">{{ $config ?? old($key) }}</textarea>
                                        @if ($errors->has($key))
                                            <span class="help-block"><strong>{{ $errors->first($key) }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($key == 'about_us')
                                <div class="form-group {{ $errors->has($key) ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label title="" >关于我们</label>&nbsp;
                                        <textarea id="questionContent_{{$key}}" name="{{ $key }}" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="关于我们">{{ $config ?? old($key) }}</textarea>
                                        @if ($errors->has($key))
                                            <span class="help-block"><strong>{{ $errors->first($key) }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($loop->last)
                                {{-- Buttons --}}
                                <p style="margin-bottom: 50px"></p>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        {{--<a href="{{ url('portal/siteConf') }}" class="btn btn-default">返回</a>--}}
                                        <button type="submit" class="btn btn-default pull-right">确定</button>
                                    </div>
                                </div>
                                @endif
                        @empty
                            <div class="noDataValue text_c">
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
    <script type="text/javascript" src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/ckfinder/ckfinder.js') }}"></script>
    <script>
        $(function () {
            CKEDITOR.replace('questionContent_currency_intro', {
                height:'150px',
            });
            CKEDITOR.replace('questionContent_privacy_policy', {
                height:'150px',
            });
            CKEDITOR.replace('questionContent_token_apply_intro', {
                height:'150px',
            });

            CKEDITOR.replace('questionContent_disclaimer', {
                height:'150px',
            });
            CKEDITOR.replace('questionContent_about_us', {
                height:'150px',
            });
        });

    </script>
@endsection
