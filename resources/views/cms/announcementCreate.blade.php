@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑公告' : '添加公告' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("cms/announcement/$announcement->id") : url('cms/announcement') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- title --}}
                        <div class="row">
                            <div class="col-md-12">
                            <div class="form-group {{ $errors->has('anno_title') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>公告标题</label>
                                    <input class="form-control input-lg" type="text" name="anno_title" value="{{ $announcement->anno_title ?? old('anno_title') }}"
                                           placeholder="请填写公告标题" required>
                                    @if ($errors->has('anno_title'))
                                        <span class="help-block"><strong>{{ $errors->first('anno_title') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>

                        {{-- summary --}}
                        <div class="row">
                            <div class="col-md-12">
                            <div class="form-group {{ $errors->has('anno_summary') ? 'has-error' : '' }}">
                                <div class="col-sm-12">
                                    <label>公告摘要</label>
                                    <textarea class="form-control" name="anno_summary" rows="5"
                                              placeholder="请填写公告摘要">{{ $announcement->anno_summary ?? old('anno_summary') }}</textarea>
                                    @if ($errors->has('anno_summary'))
                                        <span class="help-block"><strong>{{ $errors->first('anno_summary') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>


                        {{--公告内容--}}
                        <div class="row">
                            <div class="col-md-12">
                                <label>公告内容</label>
                                <div class="form-group {{ $errors->has('anno_content') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <textarea id="questionContent" name="anno_content" rows="8" cols="150" style="visibility: hidden; display: none;"
                                                  placeholder="请填写公告内容">{{ $announcement->anno_content ?? old('anno_content') }}</textarea>

                                        @if ($errors->has('anno_content'))
                                            <span class="help-block"><strong>{{ $errors->first('anno_content') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--状态/置顶--}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label>是否发布</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="anno_draft" id="anno_draft_1" value=1  {{ @$editFlag ? ($announcement->anno_draft ==1 ? 'checked ' :'') : 'checked' }}
                                                       onclick="javascript:$('#anno_top_2').prop('checked',true); $('#anno_top_1').attr('disabled',true);">草稿
                                            </label>
                                            <label class="pull-right">
                                                <input type="radio" name="anno_draft" id="anno_draft_2" value=2  {{ @$editFlag ? ($announcement->anno_draft ==2 ? 'checked ' :'') : '' }}
                                                       onclick="javascript: $('#anno_top_1').attr('disabled',false);">发布
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label>是否置顶</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="anno_top" id="anno_top_2" value=2  {{ @$editFlag ? ($announcement->anno_top ==2 ? 'checked ' :'') : 'checked' }} >不置顶
                                            </label>
                                            <label class="pull-right">
                                                <input type="radio" name="anno_top" id="anno_top_1" value=1  {{ @$editFlag ? ($announcement->anno_top ==1 ? 'checked ' :'') : '' }}>置顶
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('cms/announcement') }}" class="btn btn-default">返回</a>
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
    <script type="text/javascript" src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/ckfinder/ckfinder.js') }}"></script>
    <script>
        $(function () {
            CKEDITOR.replace('questionContent', {
                height:'300px',
            });

            if( $('#anno_draft_1').prop('checked') ){
                $('#anno_top_1').attr('disabled',true)
            }

        })
    </script>
@endsection
