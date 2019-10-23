<?php

namespace App\Http\Controllers\User;

use App\Models\Currency;
use App\Models\OTC\OtcConfig;
use App\Models\OTC\Trade;
use App\Models\UserFeeConfig;
use App\Models\Wallet\Balance;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
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
            //$confirm = $this->frozen($trader);
            if ($trader->pid == $pid) {
                $html .= '<li uid="' . $trader->id . '" pid="'.$trader->pid.'" path="' . @$trader->path .($trader->id==88?'" class="collapsable"':''). '">'.
                    ($trader->leader_level > 0 ? '<i class="fontello-flag" title="领导人"></i>':'').'
                    <a title="查看收益记录" href="'.url("user/trader/income/$trader->id").'" '.($pid == 0 ? "class='topOne'" : "")
                    .'onclick="nodeShow('.$trader->id.')">' .($trader->username?:($trader->phone?:$trader->email)).'</a>'./*$confirm.*/$modal;
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
    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $searchContributor = trim($request->searchContributor,'');
        $searchRemark = trim($request->searchRemark,'');
        $searchTransaction = trim($request->searchTransaction,'');
        $start = trim($request->start,'');
        $end = trim($request->end,'');
        $orderC = trim($request->orderC ?: 'desc','');

        $search = $searchContributor || $searchRemark || $searchTransaction ||  $start || $end;

        $bonuses = $user->bonuses()->with(['contributor'])
            ->when($searchContributor, function ($query) use ($searchContributor) {
                $query->whereHas('contributor', function ($query) use ($searchContributor) {
                     $query->where('username','like',"%$searchContributor%")
                        ->orWhere('phone','like',"%$searchContributor%")
                        ->orWhere('email','like',"%$searchContributor%");
                });
            })
            ->when($searchRemark, function ($query) use ($searchRemark) {
                 $query->where('remark','like',"%$searchRemark%");
            })
            ->when($searchTransaction, function ($query) use ($searchTransaction) {
                 $query->where('transaction_id', $searchTransaction);
            })
            ->when($start, function ($query) use ($start){
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end){
                return $query->where('created_at', '<=', $end);
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('created_at', $orderC);
            })
            ->get();

        $statistics = $this->sum($bonuses);
        $bonuses = self::selfPage($bonuses, config('app.pageSize'));

        return view('user.userBonusesIndex', compact('bonuses', 'user','search','statistics'));
    }

    /**
     * 自定义分页
     *
     * @param $items
     * @param $perPage
     * @return LengthAwarePaginator
     */
    public static function selfPage($items, $perPage)
    {
        $pageStart = request('page', 1);
        $offSet    = ($pageStart * $perPage) - $perPage;
        // $itemsForCurrentPage = array_slice($items, $offSet, $perPage, TRUE);
        $itemsForCurrentPage = $items->slice($offSet, $perPage);
        return new LengthAwarePaginator( $itemsForCurrentPage, $items->count(), $perPage,
            Paginator::resolveCurrentPage(),
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * 搜索统计
     *
     * @param $bonuses
     * @return array
     */
    public function sum($bonuses)
    {
        //bcscale(config('app.bcmath_scale'));

        list($totalAmount, $totalContribution, $totalTransaction)= [0, 0, 0];

        foreach ($bonuses ?? [] as $key => $item){
            $totalAmount += $item->amount;
            $totalContribution += $item->total;
            $totalTransaction += $item->transaction_amount;
        }

        return compact('totalAmount','totalContribution', 'totalTransaction');
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
        <a href=\"####\"  class=\"\" data-toggle=\"modal\" data-target=\"#exampleModalLong$key\">
        <i  title=\"编辑\" class=\"fa fa-edit\" style=\"color:#ccc\"></i></a >
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
                                        <select name='leader_level' id='leader_level' class='form-control input-medium' ".(@$trader->pid!=0?'disabled':'').">
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
                                        <label>充值手续费领导人分润比例（百分比）</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='percentage_leader'
                                               name=\"percentage_leader\" 
                                               value=\"".(@$trader->feeConfig->percentage_leader??old('percentage_leader'))."\"  placeholder='请填写领导人手续费分润比例'>
                                    </div>
                                     
                                     <div class=\"col-md-6\">
                                        <br>
                                        <label>充值手续费平台分润比例（百分比）</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='percentage_sys'
                                               name=\"percentage_sys\"
                                               value=\"".(@$trader->feeConfig->percentage_sys??old('percentage_sys'))."\"  placeholder='请填写平台手续费分润比例'>
                                    </div>
                                    
                                    <div class=\"col-md-12\" style='margin-top: 10px'>
                                        <label>领导人团队入金交易手续费（百分比）</label>
                                        <input class=\"form-control input-medium\" type=\"text\" id='team'
                                               name=\"team\" value=\"".(@$trader->feeConfig->team??old('team'))."\"  
                                               placeholder='领导人团队交易手续费比例' ".(@$trader->pid!=0?'disabled':'').">
                                    </div>
                                       
                                    <div class=\"col-md-12\">
                                        <label>所属领导人</label>
                                        <select name='leader_id' id='leader_id' class='form-control input-medium' ".(@$trader->pid==0?'disabled':'').">".
                                            (@$this->leader($trader))."
                                        </select>
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
     * 冻结账户
     *
     * @param $trader
     * @return string
     */
    public function frozen($trader)
    {
        $user = User::findOrFail($trader->id);
        list($icon, $title) = ['fontello-lock', '冻结'];
        if ($user->is_frozen) {
            list($icon, $title) = ['fontello-lock-open', '取消冻结'];
        }

        $confirm = "
        <a href=\"####\" onclick=\"itemUpdate(".$trader->id.",'".url("user/account/frozen/$trader->id")."','is_frozen',
            ".($user->is_frozen??0).",'用户的账户为<b><strong> ".$title." </strong></b> 状态', '"
            .csrf_token()."', '冻结 - ".str_limit($trader->username ?: ($trader->phone ?: $trader->email), 30)."');\">
            <i class=\"$icon\" style=\"color:#ccc\" title=\"".$title."账户\"></i></a>";

        return $confirm;
    }

    /**
     * 处理领导人信息
     *
     * @param $trader
     * @param array $leaders
     * @return string
     */
    public function leader($trader, $leaders=[1,2,3])
    {
        $options = "<option value=''>请选择所属领导人</option>";
        $leaders = User::getLeaders();
        //dd($leaders);
        foreach ($leaders as $key => $leader) {
            $options .= "<option value='$leader->id'".(@$trader->leader_id==$leader->id?'selected':'').">".
                ($leader->username?:$leader->phone?:$leader->email)."(UID: #$leader->id)</option>";
        }

        return $options;
    }

    /**
     * 更新领导人及手续费配置
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        bcscale(config('app.bcmath_scale'));

        // 验证
        $validator = $this->traderIncomeValidator($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($id);
        $balance = Balance::lockForUpdate()->firstOrNew(['user_id'=>$user->id,'user_wallet_currency_id' => Currency::USDT]);
        $margin = OtcConfig::leaderMargin();

        // 账户可用余额是否充足
        if ($balance->user_wallet_balance <= $margin) {
            return back()->withErrors(['marginError' => '账户余额不足-无法扣除押金'])->withInput();
        }

        \DB::transaction(function () use ($user, $balance, $margin, $request){
            // 领导人设置
            if ($request->leader_level != $user->leader_level && $request->leader_level == User::LEADER_LEVEL_ONE) {
                $user->leader_level = $user->pid == 0 ? $request->leader_level : User::COMMON; // 是否领导人
                if ($user->pid == 0) {
                    $user->leader_id =  $user->id; // 领导人id
                }

                // 更新账户类型及保证金相关数据
                $user->account_type = User::TYPE_LEADER;
                $user->margin_amount = $margin;
                $user->is_margin = User::IS_MARGIN;

                if ($user->is_margin == User::NOT_MARGIN) {
                    $balance->user_wallet_balance = bcsub($balance->user_wallet_balance, $margin);
                    $balance->save();
                }
            }

            // 领导人取消
            if ($request->leader_level != $user->leader_level && $request->leader_level = User::COMMON) {
                $user->leader_level = User::COMMON; // 普通成员
                $user->leader_id = User::COMMON;

                // 更新保证金相关数据及账户类型
                if ($user->is_margin == User::IS_MARGIN) {
                    $balance->user_wallet_balance = bcadd($balance->user_wallet_balance, $user->margin_amount);
                    $balance->save();
                }

                $user->account_type = User::TYPE_USER;
                $user->margin_amount = 0;
                $user->is_margin = User::NOT_MARGIN;
            }

            // 修改所属领导人及相关信息
            if ($request->leader_id && ($user->leader_id !=$request->leader_id)) {
                $newLeader = User::find($request->leader_id);
                $srcLeader = User::find($user->leader_id);

                $user->leader_id = $request->leader_id;
                $user->pid = $request->leader_id;
                $user->depth = $newLeader->depth + 1;

                // 原领导人存在 - 即修改所属领导人则更新其邀请人数
                if ($srcLeader) {
                    $srcLeader->decrement('invite_count');
                    $srcLeader->save();
                }

                $newLeader->increment('invite_count');
                $newLeader->save();
            }

            $user->save();

            // 手续费设置
            if ($request->percentage_total) {
                $user->feeConfig()->updateOrCreate(['user_id' => $user->id], [
                    'percentage_total'  => $request->percentage_total ?:0,
                    'percentage_sys'    => $request->percentage_sys ?:0,
                    'percentage_leader' => $request->percentage_leader ?:0,
                    'team'              => $request->team ?:0,
                ]);
            }
        });


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
            'percentage_total'  => $request->percentage_total,
            'percentage_sys'    => $request->percentage_sys,
            'percentage_leader' => $request->percentage_leader,
            'team'              => $request->team,
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
        $leaderRule = $request->decConf ? 'required' : 'nullable';

        // 验证
        $validator = Validator::make($request->all(),[
            'leader_level'      => 'sometimes|in:0,1',
            'percentage_total'  => $leaderRule.'|numeric|min:0',
            'percentage_sys'    => $leaderRule.'|numeric|min:0',
            'percentage_leader' => $leaderRule.'|min:0',
            'team'              => $leaderRule.'|min:0',
            'leader_id'         => 'sometimes|min:0',
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

    /**
     * 币商账户冻结（含普通用户、领导人、搬砖工）
     *
     * @param $uid
     * @return array
     */
    public function accountFrozen($uid)
    {
        //dd($uid);
        /*
         * 3.1.1.1 搬砖工账号冻结
            为确保交易安全，后台管理员可以冻结（禁用/停用）搬砖工账号，冻结后的影响如下：
            停止派单，该搬砖工将无法收到来自用户的下单
            资产冻结，搬砖工的资产账户将被冻结，无法发起提币操作
            广告冻结，搬砖工将无法创建广告，购入或出售 USDT，已有广告强制下架
            正在进行的交易订单，强制撤回（具体的撤回办法有待考虑）
           3.1.1.2 领导人账号冻结
            后台管理员可以对领导人账号进行冻结，冻结后的影响如下：
            资产冻结，领导人资产账户被冻结，无法发起提币操作
            停止交易，搬砖工购买 USDT 订单无法向该领导人派送
            广告冻结，领导人无法创建新的广告，现有广告被强制下架，资产退回冻结账户
            正在进行的交易订单，强制撤回（具体的撤回办法有待考虑）
         */

        $user = User::findOrFail($uid);

        $user->is_valid = $user->is_valid == User::ACTIVE ? User::FORBIDDEN : User::ACTIVE;
        $user->save();

        return ['code' => 0, 'msg' => '已冻结'];

        // 搬砖工和领导人账户冻结或解冻 - 广告下架或上架
        // 判断广告是否存在
        /*$trade = $user->trades()
            ->whereIn('status', [Trade::ON_SALE, Trade::OFF])
            ->find($uid);

        $frozen = DB::transaction(function () use ($user, $trade){

            $success =  ['code' => 0, 'msg' => '已冻结'];

            // 广告不存在
            if (!$trade) {
                $user->is_valid = $user->is_valid == User::ACTIVE ? User::FORBIDDEN : User::ACTIVE;
                $user->save();
                return $success;
            }

            // 广告下的订单是否被申诉且未处理完结-暂不能上架
            if ($trade->status == Trade::OFF && $trade->appealOrders()->count()) {
                return (['code' => 302, 'msg' => '广告下的订单正在申诉中 - 暂无法操作']);
            }

            // 判断是否有正在进行中的订单
            if ($trade->pendingOrders()->count()) {
                return (['code' => 302, 'msg' => '该广告有正在交易的订单，请在订单完成后操作']);
            }

            // 广告上架/下架状态
            $trade->status = $trade->status == Trade::ON_SALE ? Trade::OFF : Trade::ON_SALE;
            $trade->save();

            $user->is_valid = $user->is_valid == User::ACTIVE ? User::FORBIDDEN : User::ACTIVE;
            $user->save();

            return $success;
        });

        return  $frozen;*/
    }

}
