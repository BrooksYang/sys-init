<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\TraderIncomeRequest;
use App\Traits\Children;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TraderIncomeController extends Controller
{

    use Children;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 获取币商相关信息
        $traders = User::getTradersInfo();

        // 遍历币商层级
        $tree = $this->tree($traders);

        return view('user.traderLevelIndex', compact('tree'));
    }

    /**
     * 遍历节点树
     *
     * @param $traders
     * @param int $pid
     * @param string $flag
     * @param string $html
     * @return string
     */
    private function tree($traders, $pid = 0,  $flag='', $html = '')
    {
        $html .= '<ul'.($flag?'':' id="browser" class="filetree"').'>';
        foreach ($traders as $key=>$trader) {
            $modal = $this->edit($trader, $key);
            if ($trader->pid == $pid) {
                $html .= '<li uid="' . $trader->id . '" pid="'.$trader->pid.'" path="' . @$trader->path .($trader->id==88?'" class="collapsable"':''). '">
                <a href="javascript:void(0)" '.($pid == 0 ? "class='topOne'" : "").'onclick="nodeShow('.$trader->id.')">'
                    .($trader->username?:($trader->phone?:$trader->email)).'</a>'.$modal;
                $html .= $this->tree($traders, $trader->id, true);
            }
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * 编辑币商分润信息
     *
     * @param $trader
     * @param $key
     * @return string
     */
    public function edit($trader, $key)
    {
       $modal = "
        <a href=\"####\"  class=\"\" data-toggle=\"modal\" data-target=\"#exampleModalLong$key\"><i  title=\"编辑\" class=\"fa fa-edit\"></i></a >
        <!--Modal -->
        <div class=\"modal fade\" id =\"exampleModalLong$key\" tabindex =\"-1\" role = \"dialog\" 
            aria-labelledby =\"exampleModalLongTitle$key\" aria-hidden =\"true\" >
            <div class=\"modal-dialog\" role = \"document\" >
                <div class=\"modal-content\">
                    <form action='". url('user/trader/income').'/'.$trader->id ."' role=\"form\" method=\"POST\">
                        ".csrf_field() ."
                        ".method_field('PATCH')."
                        <div class=\"modal-header\" >
                            <h5 class=\"modal-title\" id =\"exampleModalLongTitle$key\" >
                            编辑 - ".str_limit($trader->username ?: ($trader->phone ?: $trader->email), 30)."</h5 >
                            <button type =\"button\" class=\"close\" data-dismiss=\"modal\" aria-label =\"Close\" >
                                <span aria-hidden =\"true\" >&times;</span >
                            </button >
                        </div >
                        <div class=\"modal-body\" >
                            <div class=\"row\">
                                <div class=\"col-md-12\">
                                    <div class=\"col-md-6\">
                                        <label>是否为领导人</label>
                                        <select name='is_leader' id='is_leader' class='form-control input-medium'>
                                            <option value='1'".(@$trader->is_leader==1?'selected':'').">领导人</option>
                                            <option value='0'".(@$trader->is_leader==0?'selected':'').">普通成员</option>
                                        </select>
                                       
                                    </div>
    
                                    <div class=\"col-md-6\">
                                        <label>充值手续费(百分比)</label>
                                         <input class=\"form-control input-medium\" type=\"text\" name=\"deposit_fee\"
                                               value=\"".(@$trader->deposit_fee??old('deposit_fee'))."\"  placeholder='请填写充值手续费比例'>
                                    </div>
                                    
                                    <div class=\"col-md-6\">
                                        <br>
                                        <label>币商分润比例(百分比)</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='self_percentage'
                                               name=\"self_percentage\"".(@$trader->is_leader==1?' disabled ':'')." 
                                               value=\"".(@$trader->self_percentage??old('self_percentage'))."\"  placeholder='请填写币商分润比例'>
                                    </div>
                                     
                                     <div class=\"col-md-6\">
                                        <br>
                                        <label>系统分润比例(百分比)</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='sys_percentage'
                                               name=\"sys_percentage\"".(@$trader->is_leader==1?' disabled ':'')."
                                               value=\"".(@$trader->sys_percentage??old('sys_percentage'))."\"  placeholder='请填写系统分润比例'>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class=\"modal-footer\" >
                            <button type = \"button\" class=\"btn btn-secondary\" data-dismiss = \"modal\" > 关闭</button >
                            <button type=\"submit\" class=\"btn btn-secondary\">确定</button>
                        </div >
                    </form>
                </div>
            </div>
        </div>";

       return $modal;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TraderIncomeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TraderIncomeRequest $request, $id)
    {
        //dd($request->all(), $id);
    }

}
