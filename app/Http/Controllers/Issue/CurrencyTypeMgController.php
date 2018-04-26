<?php

namespace App\Http\Controllers\Issue;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const PAGE_SIZE = 20;
const CURRENCY_TYPE_TALBE = 'dcuex_coin_type';

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
        if ($search) {
            $currencyType = DB::table(CURRENCY_TYPE_TALBE)
                ->where('name','like',"%$search%")
                ->paginate(PAGE_SIZE);
        }else{
            $currencyType = \DB::table(CURRENCY_TYPE_TALBE)
                ->paginate(PAGE_SIZE);
        }

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
    public function store(Request $request)
    {
        $currencyType = $request->except(['_token','edit_flag']);
        $currencyType['created_at'] = gmdate('Y-m-d H:i:s',time());

        if (DB::table(CURRENCY_TYPE_TALBE)->insert($currencyType)) {

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
            $currencyType = DB::table(CURRENCY_TYPE_TALBE)
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
    public function update(Request $request, $id)
    {
        $updateCurrencyType = $request->except(['_token', '_method']);
        $query = DB::table(CURRENCY_TYPE_TALBE)->where('id',$id);
        if($query->first()){
            $query->update([
                'name' => $updateCurrencyType['name'],
                'intro' => $updateCurrencyType['intro'],
                'updated_at' => gmdate('Y-m-d H:i:s',time()),
            ]);
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
        if (DB::table(CURRENCY_TYPE_TALBE)->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
