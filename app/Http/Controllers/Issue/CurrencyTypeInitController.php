<?php

namespace App\Http\Controllers\Issue;

use App\Http\Requests\CurrencyInitRequest;
use App\Traits\ImgCrop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

const CURRENCY_PAGE_SIZE = 20;

/**
 * Class CurrencyTypeInitController
 * @package App\Http\Controllers\Issue
 * 系统币种管理
 */
class CurrencyTypeInitController extends Controller
{
   use ImgCrop;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $query = DB::table('dcuex_crypto_currency as currency')
            ->join('dcuex_currency_type as type','currency.currency_type_id','=','type.id');
        if ($search) {
            $currency = $query->where('currency.currency_title_cn','like',"%$search")
                ->orwhere('currency.currency_title_en','like',"%$search")
                ->select(['currency.*', 'type.title'])
                ->paginate(CURRENCY_PAGE_SIZE);
        }else{
            $currency = $query->select(['currency.*', 'type.title'])
                ->paginate(CURRENCY_PAGE_SIZE);
        }

        return view('issue.currencyIndex',['currency' => $currency]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencyType = DB::table('dcuex_currency_type')->get(['id', 'title']);

        return view('issue.currencyCreate', ['currencyType' => $currencyType]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CurrencyInitRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CurrencyInitRequest $request)
    {
        $currencyIcon = $request->except(['_token','editFlag','x','y','w','h']);
        if ($request->hasFile('currency_icon')) {
            $currencyIcon['currency_icon'] = basename($request->currency_icon->store('currencyIcon','public'));
        }
        $currencyIcon['created_at'] = gmdate('Y-m-d H:i:s',time());

        if (DB::table('dcuex_crypto_currency')->insert($currencyIcon)) {

            return redirect('issuer/currencyTypeInit');
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
        $currencyType = $currency = [];
        if ($id) {
            $currency = DB::table('dcuex_crypto_currency as currency')
                ->join('dcuex_currency_type as type','currency.currency_type_id','=','type.id')
                ->where('currency.id',$id)
                ->get(['currency.*','type.id as currency_type_id', 'type.title'])
                ->first();
        }
        if ($currency->currency_type_id) {
            $currencyType = DB::table('dcuex_currency_type')->get();
        }

        return view('issue.currencyCreate',[
            'editFlag' => true,
            'currencyType' => $currencyType,
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CurrencyInitRequest $request, $id)
    {
        $currency = $request->except(['_token','_method','editFlag','x','y','w','h']);
        $query = DB::table('dcuex_crypto_currency')->where('id',$id);
        $currency['updated_at'] = gmdate('Y-m-d H:i:s',time());

        if ($request->hasFile('currency_icon') && $request->file('currency_icon')->isValid()) {
            $currency['currency_icon'] = basename($request->currency_icon->store('currencyIcon','public'));
        }
        if (!$request->currency_icon) {
            unset($currency['currency_icon']);
        }

        if ($query->first()) {
            $query->update($currency);
        }

        return redirect('issuer/currencyTypeInit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response()->json(['code' => 100020 ,'error' => '不能删除系统交易币种']);
        /*if (DB::table('dcuex_crypto_currency')->where('id', $id)->delete()) {

            return response()->json([]);
        }*/
    }

    /**
     * 上传币种图标
     *
     * @param $dir
     */
    public function upload($dir)
    {

        $maxWidth = $maxHeight = 600;
        $this->imgUpload(base64_decode($dir), $maxWidth, $maxHeight);
    }

    /**
     * 裁剪币种图标
     *
     * @param $dir
     * @return \Illuminate\Http\JsonResponse
     */
    public function crop($dir)
    {
        return $this->imgCrop('width', 'height','imageUploadPreviewWidth', base64_decode($dir), 'cropImg');
    }
}
