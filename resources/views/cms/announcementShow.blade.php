@extends('entrance::layouts.default')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>公告详情</span>
                    </h3>
                </div>

                <div class="box-body">
                    <div class="col-lg-12">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <h4>{{ $announcement->anno_title }}</h4>
                            </div>
                            <p class="text-center">
                                作者：{{ @$announcement->name }}&nbsp;&nbsp;&nbsp;&nbsp;
                                创建时间：{{ $announcement->created_at }}&nbsp;&nbsp;&nbsp;&nbsp;
                                更新时间：{{ @$announcement->updated_at ?? '--' }}&nbsp;&nbsp;&nbsp;&nbsp;
                            </p>
                            <hr>
                            @if(!empty($announcement->anno_summary))
                            <div class="alert alert-info">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <span class="entypo-info-circled"></span>
                                <strong>摘要：</strong>&nbsp;&nbsp;{{ $announcement->anno_summary }}
                            </div>
                            @endif
                            {!! $announcement->anno_content !!}
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
