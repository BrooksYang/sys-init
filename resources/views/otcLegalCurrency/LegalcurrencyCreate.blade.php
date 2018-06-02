@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑系统法定币种' : '初始化系统法定币种' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("otc/legalCurrency/$legalCurrency->id") : url('otc/legalCurrency') }}">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        {{-- Name --}}
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>法定币种名称</label>
                                <input class="form-control input-lg" type="text" name="name" value="{{ $legalCurrency->name ?? old('name') }}"
                                       placeholder="币种名称" required>
                                @if ($errors->has('name'))
                                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Abbr --}}
                        <div class="form-group {{ $errors->has('abbr') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>法定币种缩写</label>
                                <input class="form-control input-lg" type="text" name="abbr" value="{{ $legalCurrency->abbr ?? old('abbr') }}"
                                       placeholder="币种缩写" required>
                                @if ($errors->has('abbr'))
                                    <span class="help-block"><strong>{{ $errors->first('abbr') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Country --}}
                        <div class="form-group {{ $errors->has('country') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>法币所属国家</label>
                                <input class="form-control input-lg" type="text" name="country" value="{{ $legalCurrency->country ?? old('country') }}"
                                       placeholder="法定所属国家" required>
                                @if ($errors->has('country'))
                                    <span class="help-block"><strong>{{ $errors->first('country') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Country_en --}}
                        <div class="form-group {{ $errors->has('country_en') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>国家英文名称或简写</label>
                                <input class="form-control input-lg" type="text" name="country_en" value="{{ $legalCurrency->country_en ?? old('country_en') }}"
                                       placeholder="国家英文名称或简写" required>
                                @if ($errors->has('country_en'))
                                    <span class="help-block"><strong>{{ $errors->first('country_en') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('otc/legalCurrency') }}" class="btn btn-default">返回</a>
                                <button type="submit" class="btn btn-default pull-right">确定</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
    </script>
@endsection
