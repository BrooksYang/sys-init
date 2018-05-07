@extends('entrance::layouts.default')

@section('css-part')
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/select2/css/select2.min.css') }}">
    <style>
        .select2-selection.select2-selection--multiple, .select2-container--default.select2-container--focus .select2-selection--multiple{
            border-top: 0;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
            border-color: rgba(0,0,0,.12);
        }
        .select2-container .select2-search--inline .select2-search__field{
            padding-top: 7px;
            padding-bottom: 7px;
        }
        .text-messages {
            left: 0;
            top: 100%;
        }
        .text-input-danger, .text-input-danger:focus {
            border: 1px solid #ff5b5b !important;
            box-shadow: none;
        }
    </style>
    {{--@parent--}}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑 FAQ 文档' : '添加 FAQ 文档' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("faq/manage/$faq->id") : url('faq/manage') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- title --}}
                        <div class="row">
                            <div class="col-md-12">
                            <div class="form-group {{ $errors->has('faq_title') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>文档标题</label>
                                    <input class="form-control input-lg" type="text" name="faq_title" value="{{ $faq->faq_title ?? old('faq_title') }}"
                                           placeholder="填写文档标题" required>
                                    @if ($errors->has('faq_title'))
                                        <span class="help-block"><strong>{{ $errors->first('faq_title') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>

                        {{--选择类别--}}
                        <div class="row">
                            <div class="col-md-12">
                                <label>文档类别</label>
                                <div class="form-group {{ $errors->has('type_id') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control js-example-basic-multiple" id="faqType" multiple name="type_id[]" required>
                                            <option value="">请选择文档类别</option>
                                            @foreach($faqType as $key => $item)
                                                <option value="{{ $item->id }}" > {{ $item->type_title }} </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('type_id'))
                                            <span class="help-block"><strong>{{ $errors->first('type_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 类别/关键词 --}}
                        <div class="row">
                            {{--关键词--}}
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('faq_key_words') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <label>关键词</label>
                                        <input class="form-control input-sm" type="text" name="faq_key_words" value="{{ $faq->faq_key_words ?? old('faq_key_words') }}"
                                               placeholder="请填写文档关键词">
                                        @if ($errors->has('faq_key_words'))
                                            <span class="help-block"><strong>{{ $errors->first('faq_key_words') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{--文档内容--}}
                        <div class="row">
                            <div class="col-md-12">
                                <label>文档内容</label>
                                <div class="form-group {{ $errors->has('faq_content') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <textarea id="faq_content" name="faq_content" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="请填写文档内容">{{ $faq->faq_content ?? old('faq_content') }}</textarea>

                                        @if ($errors->has('faq_content'))
                                            <span class="help-block"><strong>{{ $errors->first('faq_content') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--状态--}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-2">
                                        <label>是否发布</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="is_draft" id="is_draft_1" value=1  {{ @$editFlag ? ($faq->is_draft ==1 ? 'checked ' :'') : 'checked' }}
                                                       >草稿
                                            </label>
                                            <label class="pull-right">
                                                <input type="radio" name="is_draft" id="is_draft_2" value=2  {{ @$editFlag ? ($faq->is_draft ==2 ? 'checked ' :'') : '' }}
                                                       >发布
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('faq/manage') }}" class="btn btn-default">返回</a>
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
    <script type="text/javascript" src="{{ asset('/vendor/entrance/js/select2/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/ckfinder/ckfinder.js') }}"></script>
    <script>
        $(function () {
            //select2 初始化
            $(".js-example-basic-multiple").select2({
                placeholder: "请选择选择文档类型",
            });

            //select-2 文档类型有值--绑定默认值
            var faqTypeStr = '{{ $faqTypeStr }}';
            if(faqTypeStr != ''){
                var faqTypeStr = faqTypeStr.split(',');
                $('#faqType').select2().val(faqTypeStr).trigger('change');
            }

            CKEDITOR.replace('faq_content', {
                height:'300px',
            });

        })
    </script>
@endsection
