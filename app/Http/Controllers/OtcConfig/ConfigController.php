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
            $configs = DB::table('otc_config')->get()->toArray();
            foreach ($configs as $key => $item){
                $configs[$item->key] = $item;
                unset($configs[$key]);
            }
        }

        return view('otcConfig.configCreate', [
            'editFlag' => true,
            'configKey' => $this->configKey(),
            'configs' => $configs ?? []
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $updateConfig = $request->except(['_token', '_method', 'editFlag']);
        $configKey = $this->configKey();

        Validator::make($request->all(),[
            $configKey[0] => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!str_contains($value,'|') && !is_numeric(explode('|',$value)[0]) && !explode('|',$value)[0] >=1 ) {
                        return $fail($attribute.' is invalid.');
                    }
                }
            ],
            $configKey[1] => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!str_contains($value,'|') && !is_numeric(explode('|',$value)[0]) && !explode('|',$value)[0] >=1 ) {
                        return $fail($attribute.' is invalid.');
                    }
                }
            ]
        ])->validate();

        foreach ($updateConfig as $key => $item) {
            if (in_array($key, $configKey)) {
                DB::table('otc_config')->where('key',$key)->update([
                    'value' => explode('|',$item)[0],
                    'updated_at' => gmdate('Y-m-d H:i:s',time())
                ]);
            }
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

        return  ['payment_length', 'order_cancel_frequency'];
    }
}
