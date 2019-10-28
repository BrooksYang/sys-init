@extends('entrance::layouts.default')


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

                        @if(Request::path() == 'user/manage')
                            {{--筛选认证状态--}}
                            @include('component.filter', ['url'=>url('user/manage'), 'filter'=>'filterVerify','filters'=>$userStatus['verify_status'],
                                'title'=>'筛选认证用户', 'icon'=>'fontello-menu', 'isInline'=>true])
                            {{--筛选领导人--}}
                            @include('component.filter', ['url'=>url('user/manage'),'filter'=>'filterType','filters'=>$accountType,
                                'title'=>'筛选领导人','isInline'=>true, 'mr'=>12])
                        @endif
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
                                <th>UID</th>
                                <th>用户名</th>
                                <th>电话</th>
                                <th>邮箱</th>
                                <th>身份信息</th>
                                <th>证件及交易</th>
                            <th>类型</th>
                            <th>邀请码</th>
                            <th>邀请人数</th>
                                {{--<th>真实姓名</th>
                                <th>性别</th>
                                <th>年龄</th>
                                <th>身份证号</th>
                                <th>国籍</th>--}}
                                <th>邮箱状态</th>
                                <th>手机认证</th>
                                <th>谷歌认证</th>
                                <th>认证状态</th>
                                <th>认证等级</th>
                                <th>用户状态</th>
                                {{--<th title="是否为测试账户/测试奖励获赠次数">账户</th>--}}
                                <th>注册时间&nbsp;&nbsp;<a href="{{ Request::path() != 'user/manage/pending' ? url('user/manage') : url('user/manage/pending') }}?orderC=desc">
                                        <i class="fa fa-sort-amount-desc" style="color:{{ Request::getQueryString() != 'orderC=desc' ? !Request::getQueryString() ? '' : 'gray' :''}}" title="降序"></i></a> &nbsp;
                                    <a href="{{ Request::path() != 'user/manage/pending' ? url('user/manage') : url('user/manage/pending') }}?orderC=asc">
                                        <i class="fa fa-sort-amount-asc" style="color:{{ Request::getQueryString() != 'orderC=asc' ? 'gray' : '' }}" title="升序"></i></a></th>
                                <th>操作</th>
                            </tr>
                            @forelse($user as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($user->currentPage() - 1) * $user->perPage() }}</td>
                                    <td>#{{ $item->id }}</td>
                                    <td title="{{ $item->username }}"><strong>{{ str_limit($item->username,15) ?: '--'}}</strong></td>
                                    <td title="{{$item->phone}}">{{ $item->phone?:'--' }}</td>
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
                                                        <div style="height: 55px"></div>
                                                        {{--证件开放路由--}}
                                                        <img id="" src="{{ config('app.api_res_url') }}/{{ $item->id_image_front }}" style="width:570px;border-radius:20px"
                                                             onerror="this.src='http://placehold.it/570x420'" onclick="rotate(this)"/>
                                                        <p></p>
                                                        <div style="height: 75px"></div>
                                                        <img id="" src="{{ config('app.api_res_url') }}/{{ $item->id_image_back }}" style="width:570px;border-radius:20px"
                                                             onerror="this.src='http://placehold.it/570x420'" onclick="rotate(this)"/>
                                                    </div>
                                                    <div style="height: 55px"></div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td title="其它证件信息"><a href="{{ url("user/manage/$item->id") }}?uri={{ Request::getRequestUri() }}">详情</a></td>
                                    <td>{{ @$accountType[$item->account_type]['name'] }}</td>
                                    <td>{{ $item->invite_code ?:'--' }}</td>
                                    <td>{{ $item->invite_count }}</td>
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
                                    <td>
                                        {{ $kycLevels->pluck('name','id')[$item->kyc_level_id] ?? '暂无'}}
                                    </td>
                                    <td><span class="label label-{{ $userStatus['is_valid'][$item->is_valid]['class'] }}">
                                        {{ $userStatus['is_valid'][$item->is_valid]['name'] }}</span>
                                    </td>
                                    {{--<td title="{{ $item->is_test ? '测试账户/测试奖励获赠次数'.$item->received_times.'次' : '非测试账户/测试奖励获赠次数'.$item->received_times.'次' }}">{!! $item->is_test ? '<i class="fa fa-check" title=""></i>/'.$item->received_times.'次' : '<i class="fa fa-times" title=""></i>/'.$item->received_times.'次' !!}</td>--}}
                                    <td>{{ empty($item->created_at)? '--' : $item->created_at}}</td>

                                    <td>
                                        <!-- Button trigger modal -->
                                        <a href="javascript:;"  class="" data-toggle="modal" data-target="#exampleModalLongVerify{{$key+1}}">
                                            <i class="fontello-ok" title="认证通过"></i>
                                        </a>
                                        <!-- Modal -->
                                        <div class="modal fade" id="exampleModalLongVerify{{$key+1}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongVerify{{$key+1}}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <form role="form" method="POST" action="{{ url("user/manage/$item->id") }}">
                                                    {{ csrf_field() }}
                                                    {{ method_field('PATCH') }}
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongVerifyTitle{{$key+1}}">KYC认证等级</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label>修改KYC认证等级</label>
                                                            <input type="hidden" name="field" value="kyc_level_id">
                                                            <select name="update" id="" style="width: 80%" required>
                                                                <option value="">请选择认证等级</option>
                                                                @foreach($kycLevels as $flag=>$kycLevel)
                                                                <option value="{{ $kycLevel->id }}" {{ $item->kyc_level_id == $kycLevel->id ? 'selected':'' }} >
                                                                    {{ $kycLevel->name }} - {{ '(等级值'.$kycLevel->level.')' }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has("kyc_level_id"))
                                                                <span class="help-block" style="color: #a94442"><strong>{{ $errors->first("kyc_level_id") }}</strong></span>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                                            <button type="submit" class="btn btn-secondary" >保存</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                            '{{ url("user/manage/$item->id") }}','verify_status',4,
                                            '用户账号为<b><strong> 认证失败 </strong></b> 状态',
                                            '{{ csrf_token() }}', '认证失败');"> <i class="fontello-cancel-circled" title="认证失败"></i></a>

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
                                <tr><td colspan="18" class="text-center">
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
