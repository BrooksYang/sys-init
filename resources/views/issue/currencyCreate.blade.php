@extends('entrance::layouts.default')

@section('css-import')
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/timepicker/bootstrap-timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/datepicker/datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/datepicker/clockface.css') }}">

    {{-- 图片裁剪 --}}
    <link rel="stylesheet" href="{{ url('imageCrop/css/jcrop/css/jquery.Jcrop.css') }}">
    <link rel="stylesheet" href="{{ url('imageCrop/css/image-crop.min.css') }}">
    <link rel="stylesheet" href="{{ url('imageCrop/css/jquery.fileupload.css') }}">
    <style>
        #Avatar{
            height: 200px;
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
                        <span>{{ @$editFlag ? '编辑系统代币币种' : '添加系统代币币种' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form id="formCurreny" class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("issuer/currencyTypeInit/$currency->id") : url('issuer/currencyTypeInit') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{-- Name_cn --}}
                            <div class="col-md-4">
                                <label>币种中文全称</label>
                                <div class="form-group {{ $errors->has('currency_title_cn') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="currency_title_cn" value="{{ $currency->currency_title_cn ?? old('currency_title_cn') }}"
                                               placeholder="币种中文全称">
                                        @if ($errors->has('currency_title_cn'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_title_cn') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Name_en --}}
                            <div class="col-md-4">
                                <label>币种英文全称</label>
                                <div class="form-group {{ $errors->has('currency_title_en') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="currency_title_en" value="{{ $currency->currency_title_en ?? old('currency_title_en') }}"
                                               placeholder="币种英文全称">
                                        @if ($errors->has('currency_title_en'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_title_en') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- abbr_en --}}
                            <div class="col-md-4">
                                <label>英文简写</label>
                                <div class="form-group {{ $errors->has('currency_title_en_abbr') ? 'has-error' : '' }}">
                                        <div class="col-sm-12">
                                            <input class="form-control input-lg" type="text" name="currency_title_en_abbr" value="{{ $currency->currency_title_en_abbr ?? old('currency_title_en_abbr') }}"
                                                   placeholder="发币方英文简称">
                                            @if ($errors->has('currency_title_en_abbr'))
                                                <span class="help-block"><strong>{{ $errors->first('currency_title_en_abbr') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <div class="row">
                            {{--币种类型--}}
                            <div class="col-md-6">
                                <label>币种类型</label>
                                <div class="form-group {{ $errors->has('currency_type_id') ? 'has-error' : '' }}" id="typeForm"
                                     style="display: {{ (old('type') || @$permission->group_id) ? 'none' : 'block' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control input-sm" name="currency_type_id">
                                            <option value="">请选择币种类型</option>
                                            @foreach($currencyType as $key => $item)
                                                <option value="{{ $item->id }}" {{ (@$currency->currency_type_id == $item->id|| old('currency_type_id') == $item->id) ? 'selected' : '' }}>
                                                    {{ $item->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('currency_type_id'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_type_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 发行时间--}}
                            <div class="col-md-6">
                                <label>发行时间</label>
                                <div class="form-group {{ $errors->has('currency_issue_date') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <div id="datetimepicker1" class="input-group date">
                                            <input class="form-control input-sm" name="currency_issue_date" size="16" type="text" value="{{ $currency->currency_issue_date ?? old('currency_issue_date') }}"
                                                   placeholder="发行时间" data-format="yyyy-MM-dd hh:mm:ss">
                                            <span class="input-group-addon add-on">
                                                <i style="font-style:normal;" data-time-icon="entypo-clock" data-date-icon="entypo-calendar"> </i></span>
                                        </div>
                                        @if ($errors->has('currency_issue_date'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issue_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            {{--发行数量--}}
                            <div class="col-md-6">
                                <label>发行数量</label>
                                <div class="form-group {{ $errors->has('currency_issue_amount') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="currency_issue_amount" value="{{ $currency->currency_issue_amount ?? old('currency_issue_amount') }}"
                                               placeholder="发行数量">
                                        @if ($errors->has('currency_issue_amount'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issue_amount') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--流通数量--}}
                            <div class="col-md-6">
                                <label>流通数量</label>
                                <div class="form-group {{ $errors->has('currency_issue_circulation') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="currency_issue_circulation" value="{{ $currency->currency_issue_circulation ?? old('currency_issue_circulation') }}"
                                               placeholder="流通数量">
                                        @if ($errors->has('currency_issue_circulation'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issue_circulation') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            {{--发币方官网地址--}}
                            <div class="col-md-6">
                                <label>官网地址</label>
                                <div class="form-group {{ $errors->has('currency_issuer_website') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="currency_issuer_website" value="{{ $currency->currency_issuer_website ?? old('currency_issuer_website') }}"
                                               placeholder="发币方官网地址">
                                        @if ($errors->has('currency_issuer_website'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issuer_website') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--白皮书地址--}}
                            <div class="col-md-6">
                                <label>白皮书地址</label>
                                <div class="form-group {{ $errors->has('white_paper_url') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="white_paper_url" value="{{ $currency->white_paper_url ?? old('white_paper_url') }}"
                                               placeholder="白皮书地址">
                                        @if ($errors->has('white_paper_url'))
                                            <span class="help-block"><strong>{{ $errors->first('white_paper_url') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{--钱包下载地址--}}
                            <div class="col-md-6">
                                <label>钱包下载地址</label>
                                <div class="form-group {{ $errors->has('wallet_download_url') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="wallet_download_url" value="{{ $currency->wallet_download_url ?? old('wallet_download_url') }}"
                                               placeholder="钱包下载地址">
                                        @if ($errors->has('wallet_download_url'))
                                            <span class="help-block"><strong>{{ $errors->first('wallet_download_url') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--区块查询链接--}}
                            <div class="col-md-6">
                                <label>区块查询链接</label>
                                <div class="form-group {{ $errors->has('block_chain_record_url') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="block_chain_record_url" value="{{ $currency->block_chain_record_url ?? old('block_chain_record_url') }}"
                                               placeholder="区块查询链接">
                                        @if ($errors->has('block_chain_record_url'))
                                            <span class="help-block"><strong>{{ $errors->first('block_chain_record_url') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--简介 --}}
                        <div class="row">
                            <div class="col-md-12">
                                <label>币种简介</label>
                                <div class="form-group {{ $errors->has('currency_intro') ? 'has-error' : '' }}">
                                    <div class="col-md-12">
                                        <textarea class="form-control" name="currency_intro" rows="5"
                                                  placeholder="请填写币种简介">{{ $currency->currency_intro ?? old('currency_intro') }}</textarea>
                                            @if ($errors->has('currency_intro'))
                                                <span class="help-block"><strong>{{ $errors->first('currency_intro') }}</strong></span>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        {{--<div class="form-group {{ $errors->has('currency_intro') ? 'has-error' : '' }}">
                            <div class="col-md-12">
                                <label>详细介绍</label>
                                <div class="form-group {{ $errors->has('currency_intro') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <textarea id="questionContent" name="currency_intro" rows="8" style="height: 400px" style="visibility: hidden; display: none;"
                                                  placeholder="请填写币种详细介绍">{{ $currency->currency_intro ?? old('currency_intro') }}</textarea>

                                        @if ($errors->has('currency_intro'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_intro') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>--}}

                        {{--图标--}}
                        <div class="form-group {{ $errors->has('currency_icon') ? 'has-error' : '' }}">
                            <div class="col-md-8">
                                <label>上传币种图标 <span style="color: #666;font-weight:normal;">(200*200)</span></label>
                                <p class="help-block"><small>支持jpg，jpeg，png格式，图片最大尺寸为600*600&nbsp;&nbsp;最小尺寸为80*80{{--（宽高比为1/1）--}}</small></p>
                                @if ($errors->has('currency_icon'))
                                    <span class="help-block"><strong>{{ $errors->first('currency_icon') }}</strong></span>
                                @endif
                                <input type="hidden" id="x" name="x">
                                <input type="hidden" id="y" name="y">
                                <input type="hidden" id="w" name="w">
                                <input type="hidden" id="h" name="h">
                                {{--上传图片的存储路径 不包括配置中的--}}
                                <input type="hidden" id="thumbnail" name="currency_icon" value="{{ old('currency_icon') }}">
                                <div id="Avatar">
                                    {{--图片显示路由--}}
                                    @if($editFlag ?? '')
                                        <img id="logoShow" src="{{url('currencyIcon')}}/{{ $currency->currency_icon }}" style="width:200px"
                                             onerror="this.src='http://placehold.it/180x180'"/>
                                    @else
                                        <img id="logoShow" src="{{url('currencyIcon')}}/{{ $currency->currency_icon ?? old('currency_icon') }}" style="width:200px"
                                             onerror="this.src='http://placehold.it/180x180'"/>
                                    @endif
                                </div>
                                <div class="buttonA">
                                    <div class="pull-left" style="position: relative;">
                                        <button type="button" class="chooseB">选择图片</button>
                                        {{--上传路由--}}
                                        <input id="fileupload" type="file" name="files[]" data-url="" multiple class="fileInput">
                                    </div>
                                    <div class="pull-left" style="margin-left: 10px;">
                                        <button type="button" class="crop cropB" disabled>裁剪</button>
                                    </div>
                                </div>
                            </div>
                            <div style="text-align: right;" id="upload_progress"></div>
                            <div class="spacer-15"></div>
                        </div>
                        {{--<div class="form-group {{ $errors->has('currency_icon') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>上传币种图标</label>
                                <input type="file" class="" name="currency_icon" value="" id="imgPicker">
                                --}}{{--<input type="hidden" class="" name="currency_icon">--}}{{--
                                --}}{{--<input class="form-control input-lg" type="file" name="file"
                                       placeholder="币种图标">--}}{{--
                                <p class="help-block"><small>支持jpg，jpeg，png格式，图片最小尺寸为80*80（宽高比为1/1）</small></p>
                                @if ($errors->has('currency_icon'))
                                    <span class="help-block"><strong>{{ $errors->first('currency_icon') }}</strong></span>
                                @endif
                                <div id="image" style="width:180px; background:#CCCCCC; float:left;">
                                    @if($editFlag ?? '')
                                        <img id="preview" src="{{url('currencyIcon')}}/{{ $currency->currency_icon }}" style="width:180px"
                                             onerror="this.src='http://placehold.it/180x180'"/>
                                    @else
                                        <img id="preview" src="{{url('currencyIcon')}}/{{ $currency->currency_icon ?? old('currency_icon') }}" style="width:180px"
                                             onerror="this.src='http://placehold.it/180x180'"/>
                                    @endif
                                </div>
                            </div>
                        </div>--}}

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('issuer/currencyTypeInit') }}" class="btn btn-default">返回</a>
                                <div class="pull-right"><button type="submit" class="btn btn-default pull-right">确定</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/timepicker/bootstrap-timepicker.js') }}'></script>
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/datepicker/bootstrap-datepicker.js') }}'></script>
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/datepicker/clockface.js') }}'></script>
    <script type='text/javascript' src='{{ asset('vendor/entrance/js/datepicker/bootstrap-datetimepicker.js') }}'></script>



    <script type="text/javascript" src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/ckfinder/ckfinder.js') }}"></script>

    {{--上传、裁剪--}}
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.ui.widget.js') }}"> </script>
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.iframe-transport.js') }}"></script>
    <script type="text/javascript" src="{{ asset('imageCrop/js/jquery.fileupload.js') }}"></script>
    <script type="text/javascript" src="{{ asset('imageCrop/css/jcrop/js/jquery.Jcrop.min.js') }}"></script>
    <script>
        $(function(){
          /*  CKEDITOR.replace('questionContent', {
                height:'300px',
            });*/

            //日期时间插件
            $('#datetimepicker1').datetimepicker({
                language: 'zh'
            });
           /* $("#datetimepicker1").datetimepicker({
                format: 'yyyy-mm-dd',//显示格式
                todayHighlight: 1,//今天高亮
                minView: "month",//设置只显示到月份
                startView:2,
                forceParse: 0,
                showMeridian: 1,
                autoclose: 1//选择后自动关闭
            });*/

           /* document
                .querySelector('#imgPicker')
                .addEventListener('change', function(){
                    //当没选中图片时，清除预览
                    if(this.files.length === 0){
                        document.querySelector('#preview').src = '';
                        return;
                    }
                    //实例化一个FileReader
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        //当reader加载时，把图片的内容赋值给
                        document.querySelector('#preview').src = e.target.result;
                    };

                    //读取选中的图片，并转换成dataURL格式
                    reader.readAsDataURL(this.files[0]);
                },false);*/

            var logoOld = $('#thumbnail').val();
            /*配置项：图片上传、显示、裁剪路由 url('自定义')*/
            var image_uplod_route = '{{ url('issuer/currencyIcon/upload').'/'.base64_encode('app/public/currencyIcon')}}';
            var image_view_route = '{{url('currencyIcon')}}';
            var image_crop_route = '{{ url('issuer/currencyIcon/crop').'/'.base64_encode('app/public/currencyIcon') }}';
            //上传及裁剪--预览区的尺寸
            var image_upload_preview_width = 200;
            var image_crop_preview_width = 80;
            var image_crop_preview_height = 80;
            //裁剪尺寸
            var image_crop_width = 200;
            var image_crop_height = 200;

            var hasUpload = hasCrop =  0;
            $('#fileupload').attr('data-url',image_uplod_route);
            if (logoOld) $('#logoShow').attr('src', image_view_route + '/' +logoOld);

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
                    if(!fileError && filename){ hasUpload = filename;}
                    var UrlLocation = image_view_route + '/' + filename; //文件存储完整路径
                    $("#thumbnail").val(filename); //文件存储路径(需拼接上配置跟路径)
                    Avatar.empty(); // 更改图片后再次初始化  确保图片不变形
                    //以下为原始图片预览区（width: 200px）及裁剪图片预览区(width: 50px; height: 50px;)
                    var html = `
                    <div class="responsive-1024">
                        <img src="" id="avatarCrop" alt="Example" style="width: `+image_upload_preview_width+`px;"/>
                    </div>
                    <div class="responsive-1024">
                        <div id="preview-pane" style="right: -95px;">
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
                        //以下为裁剪后图片预览区（width: 480px），与原图片预览区宽度一致
                        var html ='<img src="" alt="Example" class="" style="width: 200px;" id="avatarCropImg"/>';
                        Avatar.html(html);
                        var url = image_view_route + '/' + data.url + '?t=' + Math.random();
                        $('#avatarCropImg').attr('src', url);
                    },
                    error:function () {
                        layer.closeAll('loading');
                        layer.msg('网络错误');
                    }
                });
            })

            $('#formCurreny').submit(function () {
                var cripSize = ($("#x").val() && $("#y").val() && $("#w").val() && $("#h").val());
                if(hasUpload && (!cripSize || !hasCrop)){
                    layer.msg('请裁剪图片'); return false;
                }
            });
        });
    </script>
@endsection
