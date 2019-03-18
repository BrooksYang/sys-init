
{{-- hbfont- 样式表 --}}
<link href="{{ asset('/css/hbfont/hbfont.css') }}" rel="stylesheet" type="text/css" />
<style>
    .hbfont [class^="icon-"]:before,
    .hbfont [class*=" icon-"]:before{
        font-family: inherit;
    }
    .box-tools .fa,.box-title [class^="fontello-"]:before, [class*=" fontello-"]:before,.wrap-sidebar-content .fa,.wrap-sidebar-content [class^="icon-"]:before, [class*=" icon-"]:before{
        line-height: inherit;
    }
</style>