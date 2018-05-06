<?php

namespace App\Http\Controllers\Issue;

use App\Http\Requests\CurrencyTypeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const CURRENCY_TYPE_PAGE_SIZE  = 20;

/**
 * Class CurrencyTypeMgController
 * @package App\Http\Controllers\Issue
 * 系统代币类型管理
 */
class CurrencyTypeMgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $currencyType = DB::table('dcuex_currency_type')
            ->when($search, function ($query) use ($search){
                return $query->where('title','like',"%$search%");
            })
            ->paginate(CURRENCY_TYPE_PAGE_SIZE );

        return view('issue.currencyTypeIndex',['currencyType' => $currencyType]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('issue.currencyTypeCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CurrencyTypeRequest $request)
    {
        $currencyType = $request->except(['_token','editFlag']);
        $currencyType['created_at'] = gmdate('Y-m-d H:i:s',time());

        if (DB::table('dcuex_currency_type')->insert($currencyType)) {

            return redirect('issuer/currencyTypeMg');
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
        $currencyType = [];
        if ($id) {
            $currencyType = DB::table('dcuex_currency_type')
                ->where('id',$id)->first() ;
        }

        return view('issue.currencyTypeCreate',[
            'editFlag' => true,
            'currencyType' => $currencyType
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CurrencyTypeRequest $request, $id)
    {
        $updateCurrencyType = $request->except(['_token', '_method', 'editFlag']);
        $updateCurrencyType['updated_at'] = gmdate('Y-m-d H:i:s',time());
        $query = DB::table('dcuex_currency_type')->where('id',$id);
        if($query->first()){
            $query->update($updateCurrencyType);
        }

        return redirect('issuer/currencyTypeMg');
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
        if (DB::table('dcuex_crypto_currency')->where('currency_type_id',$id)->first()) {

            return response()->json(['code' => 100030 ,'error' => '该类型已被代币使用暂不能删除']);
        }
        if (DB::table('dcuex_currency_type')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
