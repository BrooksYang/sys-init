
{{-- date-tiempicker 视图数据 --}}
<div class="col-md-{{ $colMdNum ?? 4}}">
    @if($label)
        <label>{{ $label }}</label>
    @endif
    <div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
        <div id="datetimepicker{{$id}}" class="input-group date">
            <input class="form-control input-sm" id="{{$name}}" name="{{ $name }}" size="16" type="text" value="{{ Request::get($name)? Request::get($name): ($$name ?? old($name)??'') }}"
                   placeholder="{{ $placeholder ?? '请选择时间' }}" data-format="{{ $dataFormat ?? 'yyyy-MM-dd hh:mm:ss' }}"  >
            <span class="input-group-addon add-on">
            <i style="font-style:normal;" data-time-icon="entypo-clock" data-date-icon="entypo-calendar"> </i></span>
        </div>
        @if ($errors->has($name))
            <span class="help-block"><strong>{{ $errors->first($name) }}</strong></span>
        @endif
    </div>
</div>

