@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Add Button --}}
                    <div class="pull-right box-tools">
                        @include('component.searchForm', ['url'=>url('user/appKey'), 'placeholder'=>'用户信息或秘钥'])

                        <a href="{{ url('user/merchant/create') }}" title="添加商户">
                            <span class="box-btn"><i class="fa fa-plus"></i></span>
                        </a>
                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>商户API密钥信息列表</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>

                                <th>UID</th>
                                <th>用户</th>
                                <th>电话</th>
                                <th>邮箱</th>
                                <th>账户状态</th>

                                <th>API 访问密钥</th>
                                <th>API 签名密钥</th>
                                <th title="绑定ip后永久有效，未绑定ip过期时间为90天">绑定IP</th>
                                <th>过期时间</th>
                                <th>备注</th>
                                <th>通道状态</th>
                                <th>交易时间</th>
                                <th>创建时间
                                    @include('component.sort', ['url'=>url('user/appKey')])
                                </th>
                                <th>操作</th>
                            </tr>
                            @forelse($users as $key => $item)
                                <tr>
                                    <td>{{ ($key + 1) + ($users->currentPage() - 1) * $users->perPage() }}</td>

                                    <td><strong>{{ $item->user->id ?? '--' }}</strong></td>
                                    <td>{{ $item->user->username ?? '--' }}</td>
                                    <td><a href="{{ $item->user ? url('user/merchant').'/'.$item->user->id : '####'}}">
                                        {{ $item->user->phone ?? '--' }}</a></td>
                                    <td title="{{ $item->user->email ?? '' }}">{{ str_limit($item->user->email ?? '--',20) }}</td>
                                    <td>
                                        <span class="label label-{{ $status[$item->user->is_valid]['class'] }}">
                                            {{ $status[$item->user->is_valid]['name'] }}</span>
                                    </td>

                                    <td title="{{ $item->access_key ?: '' }}" id="copyAK{{$key}}" data-attr="{{$item->access_key}}">
                                        @include('component.copy', ['eleId'=>'copyAK'.$key, 'eleType'=>'attr', 'attr'=>'data-attr'])
                                        {{ str_limit($item->access_key ?: '--',25) }}
                                    </td>
                                    <td title="{{ $item->secret_key ?: '' }}" id="copySK{{$key}}" data-attr="{{$item->secret_key}}">
                                        @include('component.copy', ['eleId'=>'copySK'.$key, 'eleType'=>'attr', 'attr'=>'data-attr'])
                                        {{ str_limit($item->secret_key ?: '--',25) }}
                                    </td>
                                    <td title="绑定ip后永久有效，未绑定ip过期时间为90天">
                                        @if(json_decode($item->ip,true))
                                            <!-- Button trigger modal -->
                                            @include('component.modalHeader', ['modal'=>'User','title'=>'绑定IP',
                                                'icon'=>'fa fa-code', 'header'=>($item->user->username ?? '') .'-'. ($item->user->phone ?? ''),
                                                 'headerIcon'=>'fontello-user' ])
                                                <p><i class="fa fa-info-circle"></i>&nbsp;绑定ip后永久有效，未绑定ip过期时间为90天</p>
                                                <?php dump(json_decode($item->ip,true)) ?>
                                            @include('component.modalFooter',['form'=>true])
                                        @else
                                            未绑定
                                        @endif
                                    </td>
                                    <td>{{ $item->expired_at ?:  ($item->expired_at ?: '永久有效') }}</td>
                                    <td>
                                        <!-- Button trigger modal -->
                                        @include('component.modalHeader', ['modal'=>'Remark','title'=>'备注',
                                            'header'=>'信息 - '.$item->user->phone ?? $item->user->username, 'headerIcon'=>'fa fa-info-circle'])
                                        <p style="text-align: center">
                                            {{ $item->remark ?: '（空）' }}
                                        </p>
                                        @include('component.modalFooter',['form'=>false])
                                    </td>
                                    <td><span class="label label-{{ $isOpen[$item->is_enabled]['class'] }}">{{$isOpen[$item->is_enabled]['name']}}</span></td>
                                    <td>{{ $item->start_time ? $item->start_time.' - '.$item->end_time : '无限制'}}</td>
                                    <td>{{ $item->created_at ?: '--' }}</td>
                                    <td>
                                        <a href="{{ url("user/merchant/$item->id/edit") }}">
                                            <i class="fontello-edit" title="编辑"></i>
                                        </a>

                                        <a href="javascript:;" onclick="itemUpdate('{{ $item->id }}',
                                            '{{ url("change/merchant/status/$item->id") }}','is_valid',2,
                                            '商户账户为<b><strong> {{ $item->user->is_valid == \App\User::ACTIVE ? '禁用账户' : '启用账户'}} </strong></b> 状态',
                                                '{{ csrf_token() }}', '修改账户状态');">
                                            <i class="{{ $item->user->is_valid == \App\User::ACTIVE ? 'fontello-lock-filled' : 'fontello-lock-open-filled'}}"
                                               title="{{ $item->user->is_valid == \App\User::ACTIVE ? "禁用账户" : "启用账户"}}"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                @include('component.noData',['colSpan'=>15])
                            @endforelse
                        </table>

                        {{-- Paginaton --}}
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="pull-right">
                                    {{ $users->appends(Request::except('page'))->links() }}
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
