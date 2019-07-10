
<!-- Button trigger modal -->
<a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModal{{$modal}}{{$key ?? $keyNew}}" title="{{ $title ?? '' }}">
    @if( $icon ?? '') <i class="{{ $icon }}"></i> @else 查看 @endif
</a>
<!-- Modal -->
<div class="modal fade" id="exampleModal{{$modal}}{{$key ?? $keyNew}}" tabindex="-1" role="dialog" aria-labelledby="exampleModal{{$modal}}Title{{$key ?? $keyNew}}" aria-hidden="true" width="auto">
    <div class="modal-dialog" role="document" width="auto">
        <div class="modal-content" width="auto">
            @if($form ?? '')
            <form action="{{ $action }}" method="post" role="form">
                {{ csrf_field() }}
                @if($methodField ?? '')
                {{method_field($methodField)}}
                @endif
            @endif
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModal{{$modal}}Title{{$key ?? $keyNew}}"><i class="{{ $headerIcon ?? '' }}"></i>&nbsp;{{ $header ?? '' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


