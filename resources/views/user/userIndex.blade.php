@extends('entrance::layouts.default')

@section('css-part')
    @parent
@show

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Search  Button --}}
                    <div class="pull-right box-tools">
                        {{--<a href="{{ url('user/manage/create') }}">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>--}}
                        <form action="{{ Request::path() != 'user/manage/pending' ? url('user/manage') : url('user/manage/pending') }}" class="in-block">
                            <input id="search_input" type="text" class="form-control width-0" placeholder="搜索用户名或邮箱或电话" name="search" value="{{ $search }}">
                            <a href="javascript:;" title="搜索用户名或邮箱或电话">
                                <span class="box-btn" id="search-span"><i class="fa fa-search"></i></span>
                            </a>
                        </form>
                        {{--筛选测试账户--}}
                        <div style="display: inline-block;position: relative">
                            <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="按类别筛选文档">
                                <span class="box-btn"><i class="fontello-menu" title="按类别筛选文档"></i></span>
                            </a>
                            <ul role="menu" class="dropdown-menu pull-right">
                                <li>
                                    <a href="{{ url('user/manage') }}">全部
                                        {!! !Request::get('filterObj') || !Request::get('filter') ? Request::path() == 'user/manage/pending' ? '' : '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('user/manage') }}?filterObj=is_test&filter=1">测试账户
                                        {!!  Request::get('filterObj') && Request::get('filter') == 1 ? '&nbsp;<i class="fa fa-check txt-info"></i>' :
                                             Request::path() == 'user/manage/pending' &&  Request::get('filterObj') && Request::get('filter') ==1 ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <a data-toggle="dropdown" class="dropdown-toggle" type="button" title="筛选认证用户">
                            <span class="box-btn"><i class="fa fa-filter" title="筛选认证用户"></i></span>
                        </a>
                        <ul role="menu" class="dropdown-menu">
                            <li>
                                <a href="{{ url('user/manage') }}">全部
                                    {!! !Request::get('filterObj') || !Request::get('filter') ? Request::path() == 'user/manage/pending' ? '' : '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                </a>
                            </li>
                        @foreach($userStatus['verify_status'] as $key=>$item)
                            <li>
                                <a href="{{ url('user/manage') }}?filterObj=verify_status&filter={{$key}}">{{$item['name']}}
                                {!!  Request::get('filterObj') && Request::get('filter') == $key ? '&nbsp;<i class="fa fa-check txt-info"></i>' :
                                     Request::path() == 'user/manage/pending' && $key ==2 ? '&nbsp;<i class="fa fa-check txt-info"></i>' : '' !!}
                                </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>{{  Request::path() == 'user/manage/pending' ? '用户认证待审核列表' : '系统交易用户列表' }}</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>用户名</th>
                                <th>电话</th>
                                <th>邮箱</th>
                                <th>身份信息</th>
                                {{--<th>真实姓名</th>
                                <th>性别</th>
                                <th>年龄</th>
                                <th>身份证号</th>
                                <th>国籍</th>--}}
                                <th>邮箱状态</th>
                                <th>手机认证</th>
                                <th>谷歌认证</th>
                                <th>审核状态</th>
                                <th>用户状态</th>
                                <th title="是否为测试账户/测试奖励获赠次数">账户</th>
                                <th>注册时间&nbsp;&nbsp;<a href="{{ Request::path() != 'user/manage/pending' ? url('user/manage') : url('user/manage/pending') }}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::getQueryString() != 'orderC=desc' ? !Request::getQueryString() ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                    <a href="{{ Request::path() != 'user/manage/pending' ? url('user/manage') : url('user/manage/pending') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::getQueryString() != 'orderC=asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($user as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($user->currentPage() - 1) * $user->perPage() }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ empty($item->username) ? '--' : str_limit($item->username,15) }}</strong></td>
                                    <td title="{{$item->phone}}">{{ $item->phone }}</td>
                                    <td title="{{ $item->email }}"><strong>{{ str_limit($item->email,15) }}</strong></td>
                                    <td >
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLong{{$key}}">
                                            查看
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLong{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle{{$key}}" aria-hidden="true" width="auto">
                                            <div class="modal-dialog" role="document" width="auto">
                                                <div class="modal-content" width="auto">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle{{$key}}">{!!  '<i class="fontello-user-1"></i>'.$item->username !!}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <span><b>真实姓名：</b></span>{{ $item->full_name ?: '--' }}
                                                        <p></p>
                                                        <span><b>性别：</b></span>{{ $userStatus['gender'][$item->gender]['name'] }} &nbsp;&nbsp;&nbsp;
                                                        <span><b>年龄：</b></span>{{ empty($item->age) ? '--' : $item->age }} &nbsp;&nbsp;&nbsp;
                                                        <span><b>国籍：</b></span>{{ empty($item->nationality) ? '--' : $item->nationality }}
                                                        <p></p>
                                                        <span><b>身份证号：</b></span>{{ empty($item->id_number) ? '--' : $item->id_number }}
                                                        <P></P>
                                                        {{--证件开放路由--}}
                                                        <img id="" src="{{url('')}}/{{ $item->id_image_front }}" style="width:570px;border-radius:20px"
                                                             onerror="this.src='http://placehold.it/570x420'"/>
                                                        <p></p>
                                                        <img id="" src="{{url('')}}/{{ $item->id_image_back }}" style="width:570px;border-radius:20px"
                                                             onerror="this.src='http://placehold.it/570x420'"/>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td><span class="label label-{{ $userStatus['email_phone_status'][$item->email_status]['class'] }}">
                                        {{ $userStatus['email_phone_status'][$item->email_status]['name'] }}</span>
                                    </td>
                                    <td><span class="label label-{{ $userStatus['email_phone_status'][$item->phone_status]['class'] }}">
                                        {{ $userStatus['email_phone_status'][$item->phone_status]['name'] }}</span>
                                    </td>
                                    <td><span class="label label-{{ $userStatus['google_status'][$item->google_status]['class'] }}">
                                        {{ $userStatus['google_status'][$item->google_status]['name'] }}</span>
                                    </td>
                                    <td><span class="label label-{{ $userStatus['verify_status'][$item->verify_status]['class'] }}">
                                        {{ $userStatus['verify_status'][$item->verify_status]['name'] }}</span>
                                    </td>
                                    <td><span class="label label-{{ $userStatus['is_valid'][$item->is_valid]['class'] }}">
                                        {{ $userStatus['is_valid'][$item->is_valid]['name'] }}</span>
                                    </td>
                                    <td title="{{ $item->is_test ? '测试账户/测试奖励获赠次数'.$item->received_times.'次' : '非测试账户/测试奖励获赠次数'.$item->received_times.'次' }}">{!! $item->is_test ? '<i class="fa fa-check" title=""></i>/'.$item->received_times.'次' : '<i class="fa fa-times" title=""></i>/'.$item->received_times.'次' !!}</td>
                                    <td>{{ empty($item->created_at)? '--' : $item->created_at}}</td>

                                    <td>
                                        @if(in_array($item->verify_status,[1,2,4]))
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                '{{ url("user/manage/$item->id") }}','verify_status',3,
                                                '用户账号为<b><strong> 认证通过 </strong></b> 状态',
                                                '{{ csrf_token() }}', '认证通过');"> <i class="fontello-ok" title="认证通过"></i></a>
                                        @elseif($item->verify_status == 3)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                '{{ url("user/manage/$item->id") }}','verify_status',4,
                                                '用户账号为<b><strong> 认证失败 </strong></b> 状态',
                                                '{{ csrf_token() }}', '认证失败');"> <i class="fontello-cancel-circled" title="认证失败"></i></a>
                                        @endif
                                        @if($item->is_valid)
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                '{{ url("user/manage/$item->id") }}','is_valid',0,
                                                '用户账号为<b><strong> 禁用 </strong></b> 状态',
                                                '{{ csrf_token() }}', '禁用');"> <i class="fontello-lock-filled" title="禁用"></i></a>
                                        @else
                                            <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                                '{{ url("user/manage/$item->id") }}','is_valid',1,
                                                '用户账号为<b><strong> 正常 </strong></b> 状态',
                                                '{{ csrf_token() }}', '正常');"> <i class="fontello-lock-open-filled" title="解禁"></i></a>
                                        @endif
                                        <a href="javascript:;" onclick="itemDelete('{{ $item->id }}',
                                                '{{ url("user/manage/$item->id") }}',
                                                '{{ csrf_token() }}');">
                                            <i class="fontello-trash-2" title="删除"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-center">
                                    <div class="noDataValue">
                                        暂无数据
                                    </div>
                                </td></tr>
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $user->appends(Request::except('page'))->links() }}
                                </div>
                            </div>
                        </div>
                        {{-- Paginaton End --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
    </script>
@endsection
