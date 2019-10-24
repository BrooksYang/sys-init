<?php

namespace App\Http\Controllers\OtcConfig;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Class ConfigController
 * @package App\Http\Controllers\OtcConfig
 * OTC 系统交易配置
 */
class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($id) {
            $configs = \DB::table('otc_config as conf')
                //->join('auth_admins as ad', 'conf.admin_id', 'ad.id')
                ->get(['conf.*'])->toArray();
        }

        return view('otcConfig.configCreate', [
            'editFlag' => true,
            'configs' => array_chunk($configs,2) ?? []
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {

        $updateConfig = $request->except(['_token', '_method', 'editFlag']);

        Validator::make($request->all(),[
            'payment_length'                   => 'required|numeric|min:0',
            'order_cancel_frequency'           => 'required|numeric|min:0',
            'withdraw_fee_percentage'          => 'required|numeric|min:0',
            'release_order_margin'             => 'required|numeric|min:0',
            'merchant_order_fee_percentage'    => 'required|numeric|min:0',
            //'deposit_fee_percentage'         => 'required|numeric|min:0',
            'bonus_percentage_leader_buy_back' => 'required|numeric|min:0',
            'bonus_percentage_miner'           => 'required|numeric|min:0',
        ],[
            'withdraw_fee_percentage.min'      => '期望一个合法的汇率值',
            'release_order_margin.min'         => '期望一个合法的百分比值',
            //'deposit_fee_percentage.min'    => '期望一个合法的百分比值'
        ])->validate();

        foreach ($updateConfig as $key => $item) {

            \DB::table('otc_config')->where('key',$key)->update([
                'value' => $item,
                //'admin_id' => \Auth::id(),
                'updated_at' => self::carbonNow()
            ]);
        }

        return redirect("otc/config/$id/edit");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * OTC 系统配置项的键
     *
     * @return array
     */
    public function configKey()
    {

        return  [
            'payment_length',
            'order_cancel_frequency',
            'withdraw_fee_percentage',
            'release_order_margin',
            'merchant_order_fee_percentage',
            //'deposit_fee_percentage'
            'bonus_percentage_leader_buy_back' ,
            'bonus_percentage_miner',
        ];
    }
}
