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
            "use strict";
            $("#browser").treeview({
                animated: "fast",
                collapsed: true,
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
