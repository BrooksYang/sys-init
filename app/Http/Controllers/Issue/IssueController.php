<?php

namespace App\Http\Controllers\Issue;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\IssueInitRequest;
use Illuminate\Support\Facades\DB;

const  PAGE_SIZE = 20;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (trim($request->search,'')) {
            $issuers = DB::table('dcuex_issuer_account')
                ->where('name_cn','like',"%$search%")
                ->orwhere('name_en','like',"%$search%")
                ->paginate(PAGE_SIZE);
        }else{
            $issuers = \DB::table('dcuex_issuer_account')
                ->paginate(PAGE_SIZE);
        }

        return view('issue.index',['issuers' => $issuers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('issue.create',['editFlag' => false]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueInitRequest $request)
    {
        $issuer = $request->except(['_token','repeat_pwd','edit_flag']);
        $issuer['password'] = bcrypt($issuer['password']);
        $issuer['created_at'] = gmdate('Y-m-d H:i:s',time());

        if (\DB::table('dcuex_issuer_account')->insert($issuer)) {

            return redirect('issuer/issurerInit');
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
       $issuer = [];
        if ($id) {
            $issuer = DB::table('dcuex_issuer_account')
                ->where('id',$id)->first() ;
        }

        return view('issue.create',[
            'editFlag' => true,
            'issuer' => $issuer
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
        $updateIssue = $request->except(['_token', '_method']);
        $query = DB::table('dcuex_issuer_account')->where('id',$id);
        if($query->first()){
            $query->update([
                'name_cn' => $updateIssue['name_cn'],
                'name_en' => $updateIssue['name_en'],
                'abbr_en' => $updateIssue['abbr_en'],
                'addr' => $updateIssue['addr'],
                'phone' => $updateIssue['phone'],
                'updated_at' => gmdate('Y-m-d H:i:s',time()),
            ]);
        }

        return redirect('issuer/issurerInit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (DB::table('dcuex_issuer_account')->where('id', $id)->delete()) {

            return response()->json([]);
        }
    }
}
