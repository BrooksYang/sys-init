@extends('entrance::layouts.default')
<?php $model = 'announcement'; $logoName = config("imgCrop.$model.name"); ?>
@section('css-part')
    {{-- 图片裁剪 --}}
    <link rel="stylesheet" href="{{ url('imageCrop/css/jcrop/css/jquery.Jcrop.css') }}">
    <link rel="stylesheet" href="{{ url('imageCrop/css/image-crop.min.css') }}">
    <link rel="stylesheet" href="{{ url('imageCrop/css/jquery.fileupload.css') }}">
    <style>
        /*裁剪*/
        #Avatar{
            height: '{{ config("imgCrop.$model.crop.width") }}'px;
        }
        .chooseB{
            padding: 6px 12px; border: 1px solid #E3E3E3; background-color: #fff;width: 82px;
        }
        .fileInput{
            opacity: 0; width: 80px; height: 33px;  position: absolute;  top:0;  right:0;
        }
        .cropB{
            padding: 6px 12px; border: 1px solid #E3E3E3; background-color: #fff;
        }
        .buttonA{
            margin-bottom: 10px; margin-top: 10px;
        }
        button:disabled {
            cursor: not-allowed;
            pointer-events: all !important;
        }
    </style>
@endsection

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
                    <form class="form form-horizontal" id="form" role="form" method="POST" action="{{ @$editFlag ? url("cms/announcement/$announcement->id") : url('cms/announcement') }}">
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

                        {{--封面图--}}
                        <div class="form-group {{ $errors->has($logoName) ? 'has-error' : '' }}">
                            <div class="col-md-8">
                                <label>上传封面图 <span style="color: #666;font-weight:normal;">({{ config("imgCrop.$model.crop.width") }}*{{ config("imgCrop.$model.crop.height") }})</span></label>
                                <p class="help-block"><small>支持jpg，jpeg，png格式，图片推荐最小尺寸为{{ config("imgCrop.$model.crop.width") }}*{{ config("imgCrop.$model.crop.height") }}</small></p>
                                @if ($errors->has($logoName))
                                    <span class="help-block"><strong>{{ $errors->first($logoName) }}</strong></span>
                                @endif
                                <input type="hidden" id="x" name="x">
                                <input type="hidden" id="y" name="y">
                                <input type="hidden" id="w" name="w">
                                <input type="hidden" id="h" name="h">
                                {{--上传图片的存储路径 不包括配置中的--}}
                                <input type="hidden" id="thumbnail" name="{{ $logoName }}" value="{{ $editFlag ?? '' ? pathinfo($$model->$logoName)['basename'] : old($logoName) }}">
                                <div id="Avatar">
                                    {{--图片显示路由--}}

                                    @if($editFlag ?? '')
                                        <img id="logoShow" src="{{ url('')}}/{{ $$model->$logoName }}" style='width:{{config("imgCrop.$model.crop.width")}}px'
                                             onerror="this.src='http://placehold.it/{{ config("imgCrop.$model.crop.width") }}x{{ config("imgCrop.$model.crop.height") }}'" />
                                    @else
                                        <img id="logoShow" src="{{ old($logoName) ? url('').'/'.'storage/'.$model.'/'.old($logoName) :''}}" style='width:{{config("imgCrop.$model.crop.width")}}px'
                                             onerror="this.src='http://placehold.it/{{ config("imgCrop.$model.crop.width") }}x{{ config("imgCrop.$model.crop.height") }}'" />
                                    @endif
                                </div>
                                <div class="buttonA">
                                    <div class="pull-left" style="position: relative;margin-top: 10px">
                                        <button type="button" class="chooseB">选择图片</button>
                                        {{--上传路由--}}
                                        <input id="fileupload" type="file" name="files[]" data-url=""  class="fileInput">
                                    </div>
                                    <div class="pull-left" style="margin-left: 10px;margin-top: 10px">
                                        <button type="button" class="crop cropB" disabled>裁剪</button>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;" id="upload_progress"></div>
                            <div class="spacer-15"></div>
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

    {{--上传、裁剪--}}
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.ui.widget.js') }}"> </script>
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.iframe-transport.js') }}"></script>
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.fileupload.js') }}"></script>
    <script type="text/javascript" src="{{ asset('imageCrop/css/jcrop/js/jquery.Jcrop.min.js') }}"></script>


    <script>
        $(function () {
            CKEDITOR.replace('questionContent', {
                height:'300px',
            });

            if( $('#anno_draft_1').prop('checked') ){
                $('#anno_top_1').attr('disabled',true)
            }


            /*上传裁剪部分*/
            var logoOld = $('#thumbnail').val();
            /*配置项：图片上传、显示、裁剪路由 url('自定义')*/
            var image_uplod_route = '{{ url(config("imgCrop.$model.route").'upload').'/'.base64_encode('app/public/'.config("imgCrop.$model.dir")) }}';
            var image_view_route = '{{url('storage/'.config("imgCrop.$model.dir"))}}';
            var image_crop_route = '{{ url(config("imgCrop.$model.route").'crop').'/'.base64_encode('app/public/'.config("imgCrop.$model.dir")) }}';
            //上传及裁剪--预览区的尺寸
            var image_upload_preview_width = '{{ config("imgCrop.$model.preview.upload_width") }}';
            var image_crop_preview_width = '{{ config("imgCrop.$model.preview.crop_width") }}';
            var image_crop_preview_height = '{{ config("imgCrop.$model.preview.crop_height") }}';
            //裁剪尺寸
            var image_crop_width = '{{ config("imgCrop.$model.crop.width") }}';
            var image_crop_height = '{{ config("imgCrop.$model.crop.height") }}';

            //裁剪操作按钮与图片的间距-自适应
            var crop_button_margin_min = parseInt(image_crop_height)+10+'px';
            var crop_button_margin_max = parseInt(image_upload_preview_width)+10+'px';

            var hasUpload = hasCrop =  0;
            $('#fileupload').attr('data-url',image_uplod_route);
            var urlSys = '{{ url('').'/storage/'.config("imgCrop.$model.dir").'/' }}';
            if(logoOld) $('#logoShow').attr('src', urlSys+logoOld);

            $('#fileupload').fileupload({
                dataType: 'json',
                acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
                maxFileSize: 99900000000000,
                maxChunkSize: 1000000,
                maxNumberOfFiles: 1,
                done: function (e, data) {
                    var Avatar = $('#Avatar');
                    var filename = data.result.files[0].name; // 上传之后的文件名
                    var fileType = data.result.files[0].type;
                    var fileError = data.result.files[0].error;
                    //console.log(data.result.files[0]);
                    if(fileType != 'image/jpeg' && fileType !='image/png'){
                        layer.msg('不支持的图片类型');
                        return false;
                    }
                    if(fileError){ layer.msg(fileError); return false;}
                    Avatar.css('height',crop_button_margin_max);
                    if(!fileError && filename){ hasUpload = filename;}
                    var UrlLocation = image_view_route + '/' + filename; //文件存储完整路径
                    $("#thumbnail").val(filename); //文件存储路径(需拼接上配置跟路径)
                    Avatar.empty(); // 更改图片后再次初始化  确保图片不变形
                    //以下为原始图片预览区及裁剪图片预览区
                    var html = `
                    <div class="responsive-1024">
                        <img src="" id="avatarCrop" alt="Example" style="width: `+image_upload_preview_width+`px;"/>
                    </div>
                    <div class="responsive-1024">
                        <div id="preview-pane" style="right: `+-image_upload_preview_width*{{ config("imgCrop.$model.scale") }}+`px;">
                        <div class="preview-container" style="width: `+image_crop_preview_width+`px; height: `+image_crop_preview_height+`px;">
                        <img src="" class="jcrop-preview" alt="Preview" />
                    </div>
                    `;
                    Avatar.html(html);
                    $('#avatarCrop').attr('src',UrlLocation);
                    $('.jcrop-preview').attr('src',UrlLocation);
                    // Create variables (in this scope) to hold the API and image size
                    var jcrop_api,
                        boundx,
                        boundy,
                        // Grab some information about the preview pane
                        $preview = $('#preview-pane'),
                        $pcnt = $('#preview-pane .preview-container'),
                        $pimg = $('#preview-pane .preview-container img'),

                        xsize = $pcnt.width(),
                        ysize = $pcnt.height();

                    $('#avatarCrop').Jcrop({
                        onChange: updatePreview,
                        onSelect: updateCoords,
                        aspectRatio: xsize / ysize
                    },function(){
                        // Use the API to get the real image size
                        var bounds = this.getBounds();
                        boundx = bounds[0];
                        boundy = bounds[1];
                        // Store the API in the jcrop_api variable
                        jcrop_api = this;
                        // Move the preview into the jcrop container for css positioning
                        $preview.appendTo(jcrop_api.ui.holder);
                    });

                    function updateCoords(c)
                    {
                        $('#x').val(c.x);
                        $('#y').val(c.y);
                        $('#w').val(c.w);
                        $('#h').val(c.h);
                        $('.cropB').removeAttr('disabled');
                        updatePreview(c);
                    }

                    function updatePreview(c)
                    {
                        if (parseInt(c.w) > 0)
                        {
                            var rx = xsize / c.w;
                            var ry = ysize / c.h;

                            $pimg.css({
                                width: Math.round(rx * boundx) + 'px',
                                height: Math.round(ry * boundy) + 'px',
                                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                                marginTop: '-' + Math.round(ry * c.y) + 'px'
                            });
                        }
                    }
                }
            });

            $('.crop').click(function(){
                layer.load(2);
                $.ajax({
                    /*图片裁剪路由-get- url('自定义');*/
                    url:image_crop_route,
                    data:{"x":$("#x").val(),"y":$("#y").val(),"w":$("#w").val(),"h":$("#h").val(),
                        'imageUploadPreviewWidth':image_upload_preview_width,
                        "width":image_crop_width, "height":image_crop_height,
                        "cropImg":$("#thumbnail").val()},
                    cache:false,
                    type:'get',
                    success:function (data) {
                        hasCrop = true;
                        layer.closeAll('loading');
                        $('.cropB').attr('disabled','disabled');
                        var Avatar = $('#Avatar');
                        Avatar.empty();
                        //以下为裁剪后图片预览区（width: 194px），与原图片预览区宽度一致
                        var html ='<img src="" alt="Example" class="" style="width: '+image_crop_width+'px;" id="avatarCropImg"/>';
                        Avatar.html(html);
                        Avatar.css('height',crop_button_margin_min);
                        var url = image_view_route + '/' + data.url + '?t=' + Math.random();
                        $('#avatarCropImg').attr('src', url);
                    },
                    error:function () {
                        layer.closeAll('loading');
                        layer.msg('网络错误');
                    }
                });
            });

            $('#form').submit(function () {
                //if (!hasUpload && !$('#thumbnail').val()) { layer.msg('请上传资讯封面图'); return false;}
                var cripSize = ($("#x").val() && $("#y").val() && $("#w").val() && $("#h").val());
                if(hasUpload && (!cripSize || !hasCrop)){
                    layer.msg('请裁剪图片'); return false;
                }
            });

        })
    </script>
@endsection
