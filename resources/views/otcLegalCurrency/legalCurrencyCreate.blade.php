@extends('entrance::layouts.default')
<?php $model = 'flag'; $logoName = config("imgCrop.$model.name"); ?>
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
                        <span>{{ @$editFlag ? '编辑法币汇率' : '添加法币汇率（用于价格的多币种显示和汇率核算）' }}</span>
                        <i class="fontello-doc"></i>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" id="form" role="form" method="POST" action="{{ @$editFlag ? url("otc/legalCurrency/$flag->id") : url('otc/legalCurrency') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{--法币名称--}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>法币名称</label>
                                        <input class="form-control input-lg" placeholder="请填写法币名称" type="text"  required
                                               name="name" value="{{ $flag->name ?? old('name')  }}">
                                        @if ($errors->has('name'))
                                            <p style="color: red"><strong>{{ $errors->first('name') }}</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{--英文缩写--}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>英文缩写</label>
                                        <input class="form-control input-lg" placeholder="请填写法币英文缩写" type="text"  required
                                               name="abbr" value="{{ $flag->abbr ?? old('abbr')  }}">
                                        @if ($errors->has('abbr'))
                                            <p style="color: red"><strong>{{ $errors->first('abbr') }}</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{--货币符号--}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>货币符号</label>
                                        <input class="form-control input-lg" placeholder="货币符号（如￥，$）" type="text"
                                               name="symbol" value="{{ $flag->symbol ?? old('symbol') }}">
                                        @if ($errors->has('symbol'))
                                            <p style="color: red"><strong>{{ $errors->first('symbol') }}</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{--法币汇率--}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label>汇率（相对于USDT的单位价值）（即1USDT=***RMB；汇率即为***）</label>
                                        <input class="form-control input-lg" placeholder="请填写汇率" type="text"  required
                                               name="rate" value="{{ $flag->rate ?? old('rate')  }}">
                                        @if ($errors->has('rate'))
                                            <p style="color: red"><strong>{{ $errors->first('rate') }}</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="radio">
                                        <label>是否为中文版默认法币：</label>
                                        <label>
                                            <input type="radio" name="is_default_cn" value=1 >{{ $type[1]['name'] }}
                                        </label>&nbsp;&nbsp;&nbsp;
                                        <label>
                                            <input type="radio" name="is_default_cn" value=2  checked>{{ $type[2]['name'] }}
                                        </label>
                                    </div>
                                    @if ($errors->has('is_default_cn'))
                                        <p style="color: red;margin-left: 20px;" class="form-group"><strong>{{ $errors->first('is_default_cn') }}</strong></p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="radio">
                                        <label>是否为英文版默认法币：</label>
                                        <label>
                                            <input type="radio" name="is_default_en" value=1 >{{ $type[1]['name'] }}
                                        </label>&nbsp;&nbsp;&nbsp;
                                        <label>
                                            <input type="radio" name="is_default_en" value=2  checked>{{ $type[2]['name'] }}
                                        </label>
                                    </div>
                                    @if ($errors->has('is_default_en'))
                                        <p style="color: red;margin-left: 20px;"><strong>{{ $errors->first('is_default_en') }}</strong></p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{--国旗--}}
                        <div class="form-group {{ $errors->has($logoName) ? 'has-error' : '' }}">
                            <div class="col-md-8">
                                <label>上传法币-国旗图标 <span style="color: #666;font-weight:normal;">(58*35)</span></label>
                                <p class="help-block"><small>支持jpg，jpeg，png格式，图片推荐最小尺寸为58*35</small></p>
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
                                        <img id="logoShow" src="{{ url('')}}/{{ $$model->$logoName }}" style='width:{{config("imgCrop.$model.crop.width")*config("imgCrop.$model.zoom")}}px'
                                             onerror="this.src='http://placehold.it/{{ config("imgCrop.$model.crop.width") }}x{{ config("imgCrop.$model.crop.height") }}'"/>
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
                                <a href="{{ url('otc/legalCurrency') }}" class="btn btn-default">返回</a>
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

    {{--上传、裁剪--}}
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.ui.widget.js') }}"> </script>
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.iframe-transport.js') }}"></script>
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.fileupload.js') }}"></script>
    <script type="text/javascript" src="{{ asset('imageCrop/css/jcrop/js/jquery.Jcrop.min.js') }}"></script>

    <script>
        $(function () {

            /*上传裁剪部分*/
            var logoOld = $('#thumbnail').val();
            /*配置项：图片上传、显示、裁剪路由 url('自定义')*/
            var image_uplod_route = '{{ url('currency/flag/upload').'/'.base64_encode('app/public/flag')}}';
            var image_view_route = '{{url('storage/flag')}}';
            var image_crop_route = '{{ url('currency/flag/crop').'/'.base64_encode('app/public/flag') }}';
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
            var urlSys = '{{ url('').'/storage/'.$model.'/' }}';
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
                        <div id="preview-pane" style="right: `+-image_upload_preview_width*{{ config('imgCrop.flag.scale') }}+`px;">
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
            })

            $('#form').submit(function () {
                if (!hasUpload && !$('#thumbnail').val()) { layer.msg('请上传法币国旗图'); return false;}
                var cripSize = ($("#x").val() && $("#y").val() && $("#w").val() && $("#h").val());
                if(hasUpload && (!cripSize || !hasCrop)){
                    layer.msg('请裁剪图片'); return false;
                }
            });

        })
    </script>
@endsection