<?php

namespace App\Http\Controllers\Cms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

const FAQ_TYPE_PAGE_SIZE = 20;

/**
 * Class FaqTypeController
 * @package App\Http\Controllers\Cms
 * FAQ 常见问题管理
 *
 */
class FaqTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $faqType = DB::table('dcuex_faq_type')
            ->when($search, function ($query) use ($search) {
                return $query->where('type_title','like',"%$search%");
            })
            ->paginate(FAQ_TYPE_PAGE_SIZE );

        return view('cms.faqTypeIndex',['faqType' => $faqType]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('cms.faqTypeCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $faqType = $request->except(['_token','editFlag']);
        Validator::make($request->all(),[
            'type_title' => 'required|max:255',
            'type_description' => 'nullable|max:500',
        ])->validate();

        $faqType['created_at'] = gmdate('Y-m-d H:i:s',time());
        if (DB::table('dcuex_faq_type')->insert($faqType)) {

            return redirect('faq/type');
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
        $faqType = [];
        if ($id) {
            $faqType = DB::table('dcuex_faq_type')->where('id',$id)->first() ;
        }

        return view('cms.faqTypeCreate',[
            'editFlag' => true,
            'faqType' => $faqType
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
        $faqType = $request->except(['_token', '_method', 'editFlag']);
        Validator::make($request->all(),[
            'type_title' => 'required|max:255',
            'type_description' => 'nullable|max:500',
        ])->validate();

        $faqType['updated_at'] = gmdate('Y-m-d H:i:s',time());
        $query = DB::table('dcuex_faq_type')->where('id',$id);
        if($query->first()){
            $query->update($faqType);
        }

        return redirect('faq/type');
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
        if (DB::table('dcuex_faq_to_type')->where('type_id',$id)->first()) {

            return response()->json(['code' => 100030 ,'error' => '该类型已被文档使用暂不能删除']);
        }
        if (DB::table('dcuex_faq_type')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
