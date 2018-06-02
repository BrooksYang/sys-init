<?php

namespace App\Http\Controllers\OtcLegalCurrency;

use App\Http\Requests\OtcLegalCurrencyRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const  LEGAL_CURRENCY_PAGE_SIZE = 20;

/**
 * Class LegalCurrencyController
 * @package App\Http\Controllers\OtcLegalCurrency
 * OTC 系统支持的法币类型
 */
class LegalCurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $legalCurrency = DB::table('otc_legal_currencies')
            ->when($search, function ($query) use ($search){
                return $query->where('name','like',"%$search%")
                    ->orwhere('abbr', 'like',"%$search%")
                    ->orwhere('country', 'like',"%$search%");
            })
            ->paginate(LEGAL_CURRENCY_PAGE_SIZE );

        return view('otcLegalCurrency.LegalcurrencIndex',['legalCurrency' => $legalCurrency]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('otcLegalCurrency.LegalcurrencyCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OtcLegalCurrencyRequest $request)
    {
        $legalCurrency = $request->except(['_token','editFlag']);
        $legalCurrency['created_at'] = gmdate('Y-m-d H:i:s',time());

        if (DB::table('otc_legal_currencies')->insert($legalCurrency)) {

            return redirect('otc/legalCurrency');
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
            $legalCurrency = DB::table('otc_legal_currencies')
                ->where('id',$id)->first() ;
        }

        return view('otcLegalCurrency.LegalcurrencyCreate',[
            'editFlag' => true,
            'legalCurrency' => $legalCurrency ?? []
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OtcLegalCurrencyRequest $request, $id)
    {
        $legalCurrency = $request->except(['_token', '_method', 'editFlag']);
        $legalCurrency['updated_at'] = gmdate('Y-m-d H:i:s',time());

        $query = DB::table('otc_legal_currencies')->where('id',$id);
        if($query->first()){
            $query->update($legalCurrency);
        }

        return redirect('otc/legalCurrency');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //该法币是否存在交易订单
        if (DB::table('otc_advertisements')->where('legal_currency_id',$id)->first()) {

            return response()->json(['code' => 100080 ,'error' => '该法币存在交易订单暂不能删除']);
        }
        if (DB::table('otc_legal_currencies')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
