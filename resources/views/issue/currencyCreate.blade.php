@extends('entrance::layouts.default')

@section('css-import')
    {{--<link rel="stylesheet" href="{{ url('/vendor/entrance/js/datepicker/bootstrap-datetimepicker.min.css') }}">--}}
@show


@section('js-import')
   {{-- <script src="{{ url('/vendor/entrance/js/datepicker/bootstrap-datetimepicker.js') }}"></script>--}}
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">
                    {{-- Title --}}
                    <h3 class="box-title">
                        <span>{{ @$editFlag ? '编辑发行机构合约' : '添加发行机构合约' }}</span>
                    </h3>
                </div>

                {{-- Form --}}
                <div class="box-body">
                    <form class="form form-horizontal" role="form" method="POST" action="{{ @$editFlag ? url("issuer/currencyTypeInit/$currency->id") : url('issuer/currencyTypeInit') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ @$editFlag ? method_field('PATCH') : '' }}
                        <input type="hidden" name="editFlag" value="{{ @$editFlag }}">

                        <div class="row">
                            {{-- Name_cn --}}
                            <div class="col-md-4">
                                <label>币种中文全称</label>
                                <div class="form-group {{ $errors->has('currency_title_cn') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="currency_title_cn" value="{{ $currency->currency_title_cn ?? old('currency_title_cn') }}"
                                               placeholder="币种中文全称">
                                        @if ($errors->has('currency_title_cn'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_title_cn') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Name_en --}}
                            <div class="col-md-4">
                                <label>币种英文全称</label>
                                <div class="form-group {{ $errors->has('currency_title_en') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="text" name="currency_title_en" value="{{ $currency->currency_title_en ?? old('currency_title_en') }}"
                                               placeholder="币种英文全称">
                                        @if ($errors->has('currency_title_en'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_title_en') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- abbr_en --}}
                            <div class="col-md-4">
                                <label>英文简写</label>
                                <div class="form-group {{ $errors->has('currency_title_en_abbr') ? 'has-error' : '' }}">
                                        <div class="col-sm-12">
                                            <input class="form-control input-lg" type="text" name="currency_title_en_abbr" value="{{ $currency->currency_title_en_abbr ?? old('currency_title_en_abbr') }}"
                                                   placeholder="发币方英文简称">
                                            @if ($errors->has('currency_title_en_abbr'))
                                                <span class="help-block"><strong>{{ $errors->first('currency_title_en_abbr') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <div class="row">
                            {{--币种类型--}}
                            <div class="col-md-6">
                                <label>币种类型</label>
                                <div class="form-group {{ $errors->has('currency_type_id') ? 'has-error' : '' }}" id="typeForm"
                                     style="display: {{ (old('type') || @$permission->group_id) ? 'none' : 'block' }}">
                                    <div class="col-sm-12">
                                        <select class="form-control" name="currency_type_id">
                                            <option value="">请选择币种类型</option>
                                            @foreach($currencyType as $key => $item)
                                                <option value="{{ $item->id }}" {{ (@$currency->currency_type_id == $item->id|| old('currency_type_id') == $item->id) ? 'selected' : '' }}>
                                                    {{ $item->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('currency_type_id'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_type_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 发行时间--}}
                            <div class="col-md-6">
                                <label>发行时间</label>
                                <div class="form-group {{ $errors->has('currency_issue_date') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input id="form_datetime" class="form-control input-sm" name="currency_issue_date" size="16" type="text" value="{{ $currency->currency_issue_date ?? old('currency_issue_date') }}"
                                               placeholder="发行时间">
                                        @if ($errors->has('currency_issue_date'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issue_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            {{--发行数量--}}
                            <div class="col-md-6">
                                <label>发行数量</label>
                                <div class="form-group {{ $errors->has('currency_issue_amount') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="currency_issue_amount" value="{{ $currency->currency_issue_amount ?? old('currency_issue_amount') }}"
                                               placeholder="发行数量">
                                        @if ($errors->has('currency_issue_amount'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issue_amount') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--流通数量--}}
                            <div class="col-md-6">
                                <label>流通数量</label>
                                <div class="form-group {{ $errors->has('currency_issue_circulation') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="number" name="currency_issue_circulation" value="{{ $currency->currency_issue_circulation ?? old('currency_issue_circulation') }}"
                                               placeholder="流通数量">
                                        @if ($errors->has('currency_issue_circulation'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issue_circulation') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            {{--发币方官网地址--}}
                            <div class="col-md-6">
                                <label>官网地址</label>
                                <div class="form-group {{ $errors->has('currency_issuer_website') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="currency_issuer_website" value="{{ $currency->currency_issuer_website ?? old('currency_issuer_website') }}"
                                               placeholder="发币方官网地址">
                                        @if ($errors->has('currency_issuer_website'))
                                            <span class="help-block"><strong>{{ $errors->first('currency_issuer_website') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--白皮书地址--}}
                            <div class="col-md-6">
                                <label>白皮书地址</label>
                                <div class="form-group {{ $errors->has('white_paper_url') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="white_paper_url" value="{{ $currency->white_paper_url ?? old('white_paper_url') }}"
                                               placeholder="白皮书地址">
                                        @if ($errors->has('white_paper_url'))
                                            <span class="help-block"><strong>{{ $errors->first('white_paper_url') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{--钱包下载地址--}}
                            <div class="col-md-6">
                                <label>钱包下载地址</label>
                                <div class="form-group {{ $errors->has('wallet_download_url') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="wallet_download_url" value="{{ $currency->wallet_download_url ?? old('wallet_download_url') }}"
                                               placeholder="白皮书地址">
                                        @if ($errors->has('wallet_download_url'))
                                            <span class="help-block"><strong>{{ $errors->first('wallet_download_url') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{--区块查询链接--}}
                            <div class="col-md-6">
                                <label>区块查询链接</label>
                                <div class="form-group {{ $errors->has('block_chain_record_url') ? 'has-error' : '' }}">
                                    <div class="col-sm-12">
                                        <input class="form-control input-lg" type="url" name="block_chain_record_url" value="{{ $currency->block_chain_record_url ?? old('block_chain_record_url') }}"
                                               placeholder="区块查询链接">
                                        @if ($errors->has('block_chain_record_url'))
                                            <span class="help-block"><strong>{{ $errors->first('block_chain_record_url') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group {{ $errors->has('currency_intro') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>合约简介</label>
                                <textarea class="form-control" name="currency_intro" rows="5"
                                          placeholder="简介">{{ $currency->currency_intro ?? old('currency_intro') }}</textarea>
                                @if ($errors->has('currency_intro'))
                                    <span class="help-block"><strong>{{ $errors->first('currency_intro') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        {{--图标--}}
                        <div class="form-group {{ $errors->has('currency_icon') ? 'has-error' : '' }}">
                            <div class="col-sm-12">
                                <label>上传币种图标</label>
                                <input type="file" class="" name="currency_icon" value="" id="imgPicker">
                                {{--<input type="hidden" class="" name="currency_icon">--}}
                                {{--<input class="form-control input-lg" type="file" name="file"
                                       placeholder="币种图标">--}}
                                <p class="help-block"><small>支持jpg,jpeg,png格式，图片尺寸为80*80</small></p>
                                @if ($errors->has('currency_icon'))
                                    <span class="help-block"><strong>{{ $errors->first('currency_icon') }}</strong></span>
                                @endif
                                <div id="image" style="width:180px; background:#CCCCCC; float:left;">
                                    @if($editFlag ?? '')
                                        <img id="preview" src="{{url('currencyIcon')}}/{{ $currency->currency_icon }}" style="width:180px"
                                             onerror="this.src='http://placehold.it/180x180'"/>
                                    @else
                                        <img id="preview" src="{{url('currencyIcon')}}/{{ $currency->currency_icon ?? old('currency_icon') }}" style="width:180px"
                                             onerror="this.src='http://placehold.it/180x180'"/>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group">
                            <div class="col-sm-12">
                                <a href="{{ url('issuer/currencyTypeInit') }}" class="btn btn-default">返回</a>
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
        $(function(){
            //日期插件
            /*$("#form_datetime").datetimepicker({
                format: 'yyyy-mm-dd',//显示格式
                todayHighlight: 1,//今天高亮
                minView: "month",//设置只显示到月份
                startView:2,
                forceParse: 0,
                showMeridian: 1,
                autoclose: 1//选择后自动关闭
            });*/

            document
                .querySelector('#imgPicker')
                .addEventListener('change', function(){
                    //当没选中图片时，清除预览
                    if(this.files.length === 0){
                        document.querySelector('#preview').src = '';
                        return;
                    }
                    //实例化一个FileReader
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        //当reader加载时，把图片的内容赋值给
                        document.querySelector('#preview').src = e.target.result;
                    };

                    //读取选中的图片，并转换成dataURL格式
                    reader.readAsDataURL(this.files[0]);
                },false);

        });
    </script>
@endsection
