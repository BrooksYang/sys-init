
{{-- 多条件筛选--}}
{{--点击或按回车键搜索--}}
<a href="javascript:;" style="margin-right:10px;color: #fff;" class="btn btn-success" title="@lang('page.common.enter_or_click')" id="conditionSearch">
    <i class="fa fa-search"></i>&nbsp;&nbsp;{{ $button ?? trans('page.common.search') }}
</a>

<a href="{{  $url ?? '' }}" style="margin-right:8px;;color: #fff;" class="btn btn-primary" title="@lang('page.common.clean_search')" id="clear">
    {{--清空搜索项--}}
    <i class="fa fa-refresh"></i>&nbsp;&nbsp;@lang('page.common.clean_search')
</a>

