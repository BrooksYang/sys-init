<?php

namespace App\Http\Controllers\User;

use App\Models\UserFeeConfig;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TraderIncomeController extends Controller
{

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

        // 系统默认手续费分润配置
        $sysFeeConf = UserFeeConfig::sysFeeConfig();

        return view('user.traderLevelIndex', compact('tree', 'sysFeeConf'));
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
                                        <select name='leader_level' id='leader_level' class='form-control input-medium' disabled>
                                            <option value='1'".(@$trader->leader_level==1?'selected':'').">领导人</option>
                                            <option value='0'".(@$trader->leader_level==0?'selected':'').">普通成员</option>
                                        </select>
                                    </div>
    
                                    <div class=\"col-md-6\">
                                        <label>充值总手续费（百分比）</label>
                                         <input class=\"form-control input-medium\" type=\"text\" name=\"percentage_total\"
                                               value=\"".(@$trader->feeConfig->percentage_total??old('percentage_total'))."\"  placeholder='请填写充值总手续费比例'>
                                    </div>
                                    
                                    <div class=\"col-md-6\">
                                        <br>
                                        <label>领导人手续费分润比例（百分比）</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='percentage_leader'
                                               name=\"percentage_leader\" 
                                               value=\"".(@$trader->feeConfig->percentage_leader??old('percentage_leader'))."\"  placeholder='请填写领导人手续费分润比例'>
                                    </div>
                                     
                                     <div class=\"col-md-6\">
                                        <br>
                                        <label>平台手续费分润比例（百分比）</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='percentage_sys'
                                               name=\"percentage_sys\"
                                               value=\"".(@$trader->feeConfig->percentage_sys??old('percentage_sys'))."\"  placeholder='请填写平台手续费分润比例'>
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
     * @param  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // 验证
        $validator = $this->traderIncomeValidator($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($id);

        // 更新数据
        $user->feeConfig()->updateOrCreate(['user_id' => $user->id], [
            'percentage_total' => $request->percentage_total,
            'percentage_sys'   => $request->percentage_sys,
            'percentage_leader'=> $request->percentage_leader,
        ]);

        return back();
    }

    /**
     * 手续费分润 - 系统默认配置
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function defaultConf(Request $request)
    {
        // 验证
        $validator = $this->traderIncomeValidator($request);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'defConf')->withInput();
        }

        // 更新系统默认配置
        UserFeeConfig::updateOrCreate(['user_id' => 0], [
            'percentage_total' => $request->percentage_total,
            'percentage_sys'   => $request->percentage_sys,
            'percentage_leader'=> $request->percentage_leader,
        ]);

        return back();
    }

    /**
     * 手续费分润配置验证
     *
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function traderIncomeValidator($request)
    {

        // 验证
        $validator = Validator::make($request->all(),[
            'leader_level'       => 'sometimes|in:0,1',
            'percentage_total'   => 'required|numeric|min:0',
            'percentage_sys'     => 'required|numeric|min:0',
            'percentage_leader'  => 'sometimes|min:0',

        ]);

        // 钩子验证 - 币商及系统手续费设置
        $validator->after(function ($validator) use ($request){
            $sumPercentage = $request->percentage_leader + $request->percentage_sys;
            if (in_array(bccomp($sumPercentage, $request->percentage_total, 8), [1, -1])) {
                $validator->errors()->add('percentage_sys', '领导人及平台分润比例设置错误');
            }
        });

        return $validator;
    }

}
