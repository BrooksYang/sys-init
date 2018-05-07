@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>FAQ 文档详情</span>
                    </h3>
                </div>

                <div class="box-body">
                    <div class="col-lg-12">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <h4>{{ $faq->faq_title }}</h4>
                            </div>
                            <p class="text-center">
                                【关键词】：<span class="label label-info">{{ @$faq->faq_key_words }}</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                创建时间：{{ $faq->created_at ?:'--'}}&nbsp;&nbsp;&nbsp;&nbsp;
                                更新时间：{{ @$faq->updated_at ?: '--' }}&nbsp;&nbsp;&nbsp;&nbsp;
                            </p>
                            <hr>

                            <div class="alert alert-info">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <span class="entypo-info-circled"></span>
                                <strong><i class="fontello-tags"></i>文档类别：</strong>&nbsp;
                                @foreach($faqType ?? [] as $key=>$item)
                                    &nbsp;{{ $item->type_title }}&nbsp;&nbsp;
                                @endforeach
                            </div>

                            {!! $faq->faq_content !!}
                        </div>
                    </div>

                    {{-- Paginaton --}}
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right">
                                <a href="{{ URL::previous() }}" class="btn btn-default">返回</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
