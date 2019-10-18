<!-- TOP_NAV -->
<div class="row">
    <div class="col-lg-3">
        <ul class="nav navbar-nav navbar-left list-unstyled list-inline text-amber date-list">
            <li><i class="fontello-th text-amber"></i>
            </li>
            <li id="Date"></li>
        </ul>

        <ul class="nav navbar-nav navbar-left list-unstyled list-inline text-amber date-list">
            <li><i class="fontello-stopwatch text-amber"></i>
            </li>
            <li id="hours"></li>
            <li class="point">:</li>
            <li id="min"></li>
            <li class="point">:</li>
            <li id="sec"></li>
        </ul>
    </div>

    <div class="col-lg-6">
        <div style="margin-bottom:0;" class="alert text-white ">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <span class="entypo-info-circled"></span>
            <strong>{{ config('app.welcome_msg') }}</strong>
        </div>
    </div>

    <div class="col-lg-3">
        <ul class="nav navbar-nav navbar-right">
            <li>
                <a data-toggle="dropdown" class="dropdown-toggle text-white" href="#">
                    @if(Entrance::user()->role_id == config('conf.supervisor_role'))
                        <img alt="" class="admin-pic img-circle" src="{{ url('img/supervisor.jpg') }}">
                    @else
                        <img alt="" class="admin-pic img-circle" src="{{ url('vendor/entrance/img/admin.jpg') }}">
                    @endif
                    Hi, {{ Entrance::user()->name }} <b class="caret"></b>
                </a>
                <ul style="margin:25px 15px 0 0;" role="menu" class="dropdown-setting dropdown-menu bg-amber">
                    <li>
                        <a href="{{ url('lockScreen') }}"   class="text-white">
                            <i class="fontello-desktop"></i>&nbsp;&nbsp;锁屏
                        </a>
                        <a href="#" onclick="document.getElementById('logout-form').submit();"  class="text-white">
                            <i class="fontello-upload-outline"></i>&nbsp;&nbsp;退出222
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- END OF TOP_NAV -->
