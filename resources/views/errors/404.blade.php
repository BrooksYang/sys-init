@extends('entrance::layouts.default')

@section('css-part')
    @parent
@endsection

@section('content')
    <div class="wrap-fluid" id="paper-bg">

        <div class="row">
            <div class="col-lg-12">
                <div class="not-found">

                    <img class="img-responsive" alt="" src="{{ url('vendor/entrance/img/404.png') }}">

                    <p>你访问的页面不存在...

                    <form class="search-form not-found-search">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <a href="{{ url('demo')}}" class="btn bg-red">返回首页</a>
                            </div>
                        </div>
                        <!-- /.input-group -->
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
