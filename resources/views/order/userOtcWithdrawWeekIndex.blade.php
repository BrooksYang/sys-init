@extends('entrance::layouts.default')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header">

                    {{-- Filter and Add Button --}}
                    <div class="pull-right box-tools">

                    </div>

                    {{-- Title --}}
                    <h3 class="box-title"><i class="fontello-doc"></i>
                        <span>{{ Request::path() == 'order/otc/withdraw' ? '按周导出OTC用户提现记录' : ''}}</span>
                    </h3>
                </div>

                {{-- Table --}}
                <div class="box-body">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th>序号</th>
                                <th>周次</th>
                                <th>开始时间</th>
                                <th>结束时间</th>
                                <th>操作</th>
                            </tr>
                            <?php $flag = 1; ?>
                            @forelse($weeks as $key => $item)
                                @if(time() >= strtotime($item['week_start']))
                                    <tr>
                                        <td>{{$flag++}}</td>
                                        <td>第 {{ $key }} 周</td>
                                        <td>{{ $item['week_start'] }}</td>
                                        <td>{{ $item['week_end'] }}</td>
                                        <td>
                                            <a href="{{ Request::path() == 'order/otc/withdraw'
                                                        ? url('order/otc/withdraw/exportExcel')
                                                        : '' }}?start_time={{ $item['week_start']}}&end_time={{$item['week_end']}}">
                                                <i class="fontello-export" title="导出"></i></a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                @include('component.noData',['colSpan' => 5])
                            @endforelse
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-part')
    <script>
        $(function () {

        });
    </script>
@endsection
