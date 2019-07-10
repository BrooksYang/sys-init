
{{-- 筛选--}}
@if($isInline ?? '')
    <div style="display: inline-block;position: relative">
@endif
<a data-toggle="dropdown" class="dropdown-toggle" type="button" title="{{ $title ?? '筛选' }}">
    <span class="box-btn"><i class=" {{ $icon ?? 'fa fa-filter' }}" title="{{ $title ?? '筛选'}}"></i></span>
</a>
<ul role="menu" class="dropdown-menu">
    <li>
        <a href="{{ $url }}">全部
            {!! Request::get( $filter ?? 'filter') !== null && in_array( Request::get( $filter ?? 'filter'), array_keys($filters)) ? '' :'&nbsp;<i class="fa fa-check txt-info"></i>'!!}
        </a>
    </li>
    @foreach($filters as $key=>$item)
        <li>
            <a href="{{ $url }}?{{ $filter ?? 'filter' }}={{$key}}">{{$item['name']}}
                {!!  Request::get( $filter ?? 'filter') !== null && Request::get( $filter ?? 'filter') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
            </a>
        </li>
    @endforeach
</ul>
@if($isInline ?? '')
    </div>
@endif

