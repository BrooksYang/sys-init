<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\FaqRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

const FAQ_PAGE_SIZE = 20;

/**
 * Class FaqController
 * @package App\Http\Controllers\Cms
 * 系统FAQ文档管理
 *
 */
class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //文档状态
        $faqStatus = [
            1 => ['name' => '草稿' ,'class' => 'default'],
            2 => ['name' => '发布' ,'class' => 'success'],
        ];
        $faqRecommend = [
            1 => ['name' => '推荐' ,'class' => 'primary'],
            2 => ['name' => '不推荐' ,'class' => 'default'],
        ];
        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'');
        $filterType= trim($request->filterType,'');
        $filterStatus= trim($request->filterStatus,'');
        $filterRecommend= trim($request->filterRecommend,'');
        $faq = DB::table('dcuex_faq as faq')
            ->when($filterType, function ($query) use ($filterType){
                return $query->join('dcuex_faq_to_type as ftt','faq.id','ftt.faq_id')
                    ->join('dcuex_faq_type as type', 'ftt.type_id', 'type.id')
                    ->where('ftt.type_id', $filterType);
            })
            ->when($filterStatus, function ($query) use ($filterStatus){
                return $query->where('faq.is_draft', $filterStatus);
            })
            ->when($filterRecommend, function ($query) use ($filterRecommend){
                return $query->where('faq.is_recommend', $filterRecommend);
            })
            ->when($search, function ($query) use ($search){
                return $query->where('faq.faq_title','like',"%$search%")
                    ->orwhere('faq.faq_key_words', 'like', "%$search%");
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('faq.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('faq.created_at', 'desc'); //默认创建时间倒序
            })
            ->select('faq.*')
            ->paginate(FAQ_PAGE_SIZE );

        $faqType = $this->getFaqType();

        return view('cms.faqIndex', compact('faqStatus', 'faqRecommend','faqType','faq'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $faqType = $this->getFaqType();

        return view('cms.faqCreate',['editFlg' => true, 'faqType' => $faqType, 'faqTypeStr' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqRequest $request)
    {
        $faq = $request->except(['_token', 'type_id', 'editFlag']);
        $typeId = $request->type_id;
        $faq['submitter_id'] = Auth::id();
        $faq['created_at'] = gmdate('Y-m-d H:i:s',time());

        DB::transaction(function () use ($faq, $typeId) {
            $faqId = DB::table('dcuex_faq')->insertGetId($faq);
            //整理文档与类型的关联
            $faqToType = $this->sortOutFaqToType($typeId, $faqId, 'created_at');
            DB::table('dcuex_faq_to_type')->insert($faqToType);
        });

        return redirect('faq/manage');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $queryFaq = DB::table('dcuex_faq as faq')->where('faq.id', $id);
        $isAdmin = $queryFaq->first(['is_admin']);
        $faq = $queryFaq->when($isAdmin->is_admin == 1, function ($query) use ($id){
            return $query->join('auth_admins as admin','faq.submitter_id','admin.id')
                ->select(['faq.*','admin.name','admin.email']);
        })->when($isAdmin->is_admin == 2, function ($query) use ($id){
            return $query->join('users','faq.submitter_id','users.id')
                ->select(['faq.*','users.username as name','users.email','users.phone']);
        })->first();

        $faqTypeIds = DB::table('dcuex_faq_to_type')->where('faq_id', $id)->get(['type_id'])->toArray();
        $faqType = DB::table('dcuex_faq_type')->whereIn('id',array_pluck($faqTypeIds, 'type_id'))
            ->get(['type_title']);

        return view('cms.faqShow', compact('faqType', 'faq'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editFlag = true;
        //获取文档类型基本信息
        $faqType = $this->getFaqType();

        //获取文档指定类型信息
        $faqToType = $this->getFaqToType($id);
        $faqTypeStr = implode(',', array_pluck($faqToType, 'type_id'));

        $faq = DB::table('dcuex_faq as faq')->where('faq.id', $id)->get()->first();

        return view('cms.faqCreate', compact('editFlag', 'faqType', 'faqTypeStr','faq'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaqRequest $request, $id)
    {
        $faq = $request->except(['_token','_method', 'type_id','editFlag']);
        $typeId = $request->type_id;
        $faq['updated_at'] = gmdate('Y-m-d H:i:s',time());

        //获取并整理文档与类型的关联
        $faqToType = $this->sortOutFaqToType($typeId, $id, 'created_at');

        DB::transaction(function ()  use ($faq, $id, $faqToType){
            DB::table('dcuex_faq')->where('id', $id)->update($faq);
            DB::table('dcuex_faq_to_type')->where('faq_id', $id)->delete();
            DB::table('dcuex_faq_to_type')->insert($faqToType);
        });

        return redirect('faq/manage');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id){
            DB::table('dcuex_faq')->where('id', $id)->delete();
            DB::table('dcuex_faq_to_type')->where('faq_id', $id)->delete();
        });

       return response()->json([]);
    }

    /**
     * 获取文档类型基本信息
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFaqType()
    {
        return DB::table('dcuex_faq_type')->get();
    }

    /**
     * 获取指定文档的类型信息
     *
     * @param $faqId
     * @return array
     */
    public function getFaqToType($faqId)
    {
        return DB::table('dcuex_faq_to_type')->where('faq_id', $faqId)
            ->get(['type_id'])->toArray();
    }

    /**
     * 整理文档和类型的关联关系
     *
     * @param $typeId
     * @param $faqId
     * @return array
     */
    public function sortOutFaqToType($typeId, $faqId, $action)
    {
        $sortOutFaqToType = [];
        foreach ($typeId as $key => $item) {
            $sortOutFaqToType[] = [
                'faq_id' => $faqId,
                'type_id' => $item,
                'created_at' => gmdate('Y-m-d H:i:s',time())
            ];
        }

        return $sortOutFaqToType;
    }

    public function updateStatus(Request $request, $faaId)
    {
        $query = DB::table('dcuex_faq')->where('id', $faaId);
        $fqq = [
            $request->field => $request->update,
            'updated_at' => gmdate('Y-m-d H:i:s',time()),
        ];

        if ($query->update($fqq)) {

            return response()->json(['code' =>0, 'msg' => '更新成功' ]);
        }
    }
}
