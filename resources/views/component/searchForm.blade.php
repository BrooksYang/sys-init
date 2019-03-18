
{{-- 搜索框--}}
<form action="{{ $url }}" class="in-block">
    <input id="search_input" type="text" class="form-control width-0"
           placeholder="{{ \Entrance::user()->role_id ==1 ? $placeholder : $placeholderRole ?? '' }}" name="search" value="{{ $search }}">
    <a href="javascript:;" title="{{ \Entrance::user()->role_id ==1 ? $placeholder : $placeholderRole ?? '' }}">
        <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
    </a>
</form>