
{{-- 页面数据排序--}}
<a href="{{ $url }}?{{str_replace(['&&'],'&','orderC=desc&'.str_replace(['orderC=asc'],'',Request::getQueryString())  )}}">
    <i class="fa fa-sort-amount-desc"
       style="color:{{  strpos(Request::getQueryString(),'orderC=desc') !== false ?  ''
       : strpos(Request::getQueryString(),'orderC=asc') !== false ? 'gray' : '' }}" title="降序"></i></a> &nbsp;
<a href="{{ $url }}?{{ str_replace(['&&'],'&','orderC=asc&'.str_replace(['orderC=desc'],'',Request::getQueryString()) )}}">
    <i class="fa fa-sort-amount-asc" style="color:{{ strpos(Request::getQueryString(),'orderC=asc') !== false ?  ''
     : 'gray' }}" title="升序"></i></a>
