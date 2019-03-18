<?php

namespace App\Http\Controllers\OtcConfig;

use App\Http\Requests\OtcPayTypeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const OTC_PAY_TYPE_PAGE_SIZE = 20;

/**
 * Class OtcPayTypeController
 * @package App\Http\Controllers\OtcConfig
 * OTC 订单支付类型管理
 *
 */
class OtcPayTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $payType = DB::table('otc_pay_types')
            ->when($search, function ($query) use ($search){
                return $query->where('name','like',"%$search%")
                    ->orwhere('name_en', 'like', "%$search%");
            })
            ->orderBy('created_at','desc')
            ->paginate(OTC_PAY_TYPE_PAGE_SIZE );

        return view('otcConfig.payTypeIndex',['payType' => $payType]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('otcConfig.payTypeCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OtcPayTypeRequest $request)
    {
        $payType = $request->except(['_token','editFlag']);
        $payType['created_at'] = self::carbonNow();

        if (DB::table('otc_pay_types')->insert($payType)) {

            return redirect('otc/payType');
        }
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
            $payType = DB::table('otc_pay_types')
                ->where('id',$id)->first() ;
        }

        return view('otcConfig.payTypeCreate',[
            'editFlag' => true,
            'currencyType' => $payType ?? []
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OtcPayTypeRequest $request, $id)
    {
        $updatePayType = $request->except(['_token', '_method', 'editFlag']);
        $updatePayType['updated_at'] = self::carbonNow();
        $query = DB::table('otc_pay_types')->where('id',$id);

        if($query->first()){
            $query->update($updatePayType);
        }

        return redirect('otc/payType');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //该类型是否已被引用
        if (DB::table('otc_pay_paths')->where('pay_type_id',$id)->first()) {

            return response()->json(['code' => 200060 ,'error' => '该支付类型已被用户使用暂不能删除']);
        }
        if (DB::table('otc_pay_types')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
