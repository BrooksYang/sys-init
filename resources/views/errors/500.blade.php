<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!--   <meta content="IE=edge" http-equiv="X-UA-Compatible"> -->
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="" name="description">
    <meta content="" name="author">
    <link href="{{ asset('ico/favicon.ico') }}" rel="shortcut icon">

    <title>{{ config('app.name_cn', 'Laravel') }}</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/entrance/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/entrance/css/style.css') }}">


    <style>
        a:hover, a:focus { text-decoration: none }
        .a-font{
            font-size: 18px;
        }
    </style>

</head>
<body role="document">

<div class="container-fluid paper-wrap bevel tlbr">
    <div class="wrap-sidebar-content">
        <div class="wrap-fluid" id="paper-bg">
            <div class="row">
                <div class="col-lg-12">
                    <div class="not-found">

                        <img class="img-responsive" alt="" src="{{ url('vendor/entrance/img/500.png') }}">

                        <h3>服务器异常...</h3>
                        <a href="{{ url('/') }}" class="a-font">返回首页</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
