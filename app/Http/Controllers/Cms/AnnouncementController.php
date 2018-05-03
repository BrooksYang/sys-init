<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\AnnouncementRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

const ANNOUNCEMENT_PAGE_SIZE = 20;

/**
 * Class AnnouncementController
 * @package App\Http\Controllers\Cms
 * 公告管理
 */
class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //公告状态
        $announcementStatus = [
            1 => ['name' => '草稿' ,'class' => 'default'],
            2 => ['name' => '发布' ,'class' => 'success'],
        ];
        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'');
        $filter= trim($request->filter,'');
        $announcement = DB::table('dcuex_cms_announcement as anno')
            ->join('auth_admins as ad','anno.account_id','ad.id') //用户信息
            ->when($filter, function ($query) use ($filter){
                return $query->where('anno.anno_draft', $filter);
            })
            ->when($search, function ($query) use ($search){
                return $query->where('anno.anno_title','like',"%$search%")
                    ->orwhere('ad.name', 'like', "%$search%");
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('anno_top', 'asc')->orderBy('anno.created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('anno_top', 'asc')->orderBy('anno.created_at', 'desc'); //默认创建时间倒序
            })
            ->select('anno.*', 'ad.name', 'ad.email')
            ->paginate(ANNOUNCEMENT_PAGE_SIZE );

        return view('cms.announcementIndex',compact('announcementStatus','announcement'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cms.announcementCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        $announcement = $request->except(['_token','editFlag']);
        $announcement['account_id'] = Auth::id();
        $announcement['created_at'] = gmdate('Y-m-d H:i:s',time());
        if (DB::table('dcuex_cms_announcement')->insert($announcement)) {

            return redirect('cms/announcement');
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
        $announcement = DB::table('dcuex_cms_announcement as anno')
            ->join('auth_admins as ad','anno.account_id','ad.id') //用户信息
            ->where('anno.id',$id)
            ->select('anno.*', 'ad.name', 'ad.email')
            ->first();

        return view('cms.announcementShow' ,compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $announcement = DB::table('dcuex_cms_announcement')->where('id',$id)->first();
        if (empty($announcement)) {

            return redirect('cms/announcement');
        }

        return view('cms.announcementCreate',[
            'editFlag' => true,
            'announcement' => $announcement
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = $request->except(['_token','_method','editFlag']);
        $query = DB::table('dcuex_cms_announcement')->where('id', $id);
        $request->anno_top == 1 ? $announcement['created_at'] = gmdate('Y-m-d H:i:s',time()) : null;
        $announcement['account_id'] = Auth::id();
        $announcement['updated_at'] = gmdate('Y-m-d H:i:s',time());
        if ($query->update($announcement)) {

            return redirect('cms/announcement');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (DB::table('dcuex_cms_announcement')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }


    /**
     * 组合更新公告的草稿/发布状态，置顶/取消置顶状态
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse\
     */
    public function updateStatus(Request $request, $id)
    {
        $query = DB::table('dcuex_cms_announcement')->where('id', $id);
        $announcement = [
            $request->field => $request->update,
            'account_id' => Auth::id(),
            'updated_at' => gmdate('Y-m-d H:i:s',time()),
        ];

        //获取需要更新字段和 url参数字段配合使用
        if ($request->field == 'anno_draft') {
            $announcement['anno_draft'] = $request->update;
            $announcement['anno_top'] = $request->anno_top;
        }
        if ($request->field == 'anno_top') {
            $announcement['anno_top'] = $request->update;
            $announcement['anno_draft'] = $request->anno_draft;
        }

        //发布并置顶或置顶同步更新创建时间
        $request->anno_top == 1 ? $announcement['created_at'] = gmdate('Y-m-d H:i:s',time()) : null;

        if ($query->update($announcement)) {

            return response()->json(['code' =>0, 'msg' => '更新成功' ]);
        }
    }
}
