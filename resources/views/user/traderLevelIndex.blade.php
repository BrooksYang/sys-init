@extends('entrance::layouts.default')

@section('css-part')
    <link rel="stylesheet" href="{{ asset('vendor/entrance/js/tree/jquery.treeview.css') }}" />
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        <!-- Button trigger modal -->
                        <a href="####"  style="margin-right:10px;color: #fff;" class="btn btn-info" title="系统分润默认配置"
                           data-toggle="modal" data-target="#exampleModalEdit">
                            <i class="fa fa-gear"></i>&nbsp;系统分润默认配置
                        </a>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalEditTitle"
                             aria-hidden="true" width="auto">
                            <div class="modal-dialog" role="document" width="auto">
                                <div class="modal-content" width="auto">
                                    <form action="{{ url('trader/income/defConfig') }}" role="form" method="POST" >
                                        {{ csrf_field() }}
                                        {{  method_field('PATCH')}}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalEditTitle">编辑系统手续费分润默认配置</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-12">
                                                        <div class="alert alert-warning">
                                                            <button data-dismiss="alert" class="close" type="button">×</button>
                                                            <strong>
                                                                操作提示：如用户未设置相应手续费比例 - 则其手续费将依据此默认配置项
                                                            </strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label>充值总手续费（百分比）</label>
                                                        <input class="form-control input-medium" type="text" name="percentage_total"
                                                        value="{{@$sysFeeConf->percentage_total??old('percentage_total') }}"  placeholder='请填写充值总手续费比例'>
                                                        @if ($errors->defConf->has('percentage_total'))
                                                            <p class="" style="color: red;"><strong>{{ $errors->defConf->first('percentage_total') }}</strong></p>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <br>
                                                        <label>平台手续费分润比例（百分比）</label>
                                                        <input class="form-control input-medium" type="text" name="percentage_sys"
                                                               value="{{@$sysFeeConf->percentage_sys??old('percentage_sys')}}"
                                                               placeholder='请填写平台手续费分润比例'>
                                                        @if ($errors->defConf->has('percentage_sys'))
                                                            <p class="" style="color: red;"><strong>{{ $errors->defConf->first('percentage_sys') }}</strong></p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <br>
                                                        <label>领导人手续费分润比例（百分比）</label>
                                                        <input class="form-control input-medium" type="text" name="percentage_leader"
                                                               value="{{@$sysFeeConf->percentage_leader??old('percentage_leader')}}"
                                                               placeholder='请填写领导人手续费分润比例'>
                                                        @if ($errors->defConf->has('percentage_leader'))
                                                            <p class="" style="color: red;"><strong>{{ $errors->defConf->first('percentage_leader') }}</strong></p>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                            <button type="submit" class="btn btn-secondary">确定</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>系统币商</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <div class="col-sm-6">
                            {!! $tree !!}
                        </div>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">

                                </div>
                            </div>
                        </div>
                        {{-- Paginaton End --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')

    {{--<script  src="{{ asset('vendor/entrance/js/tree/lib/jquery.cookie.js') }}" type="text/javascript"></script>--}}
    <script  src="{{ asset('vendor/entrance/js/tree/jquery.treeview.js') }}" type="text/javascript"></script>
    <script>
        (function($) {

            // 验证消息
            if('{{$errors->first()}}' ||　'{{$errors->defConf->first()}}') {
                layer.msg('{{$errors->first() ?:$errors->defConf->first()}}')
            }

            "use strict";
            $("#browser").treeview({
                animated: "fast",
                collapsed: false,
                unique: true,
                //persist: "cookie",
                toggle: function() {
                    window.console && console.log("%o was toggled", this);
                }
            });

           $("#browser").find("ul").each(function () {
               if(jQuery(this).text()===''){
                   //jQuery(this).prev().prev().remove();
                   jQuery(this).parent().children().first().remove();
                   jQuery(this).parent().removeClass("collapsable");
                   jQuery(this).parent().removeClass("expandable");
                   jQuery(this).parent().removeClass("lastExpandable");
                   if (jQuery(this).parent().attr("pid") != 0 && jQuery(this).children().length=='' && jQuery(this).parent().next().length=='') {
                       //console.log(jQuery(this));
                       jQuery(this).parent().addClass("last")
                   }
                   jQuery(this).remove();
               }
           })


        })(jQuery);
    </script>
@endsection
