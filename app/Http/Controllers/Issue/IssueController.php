<?php

namespace App\Http\Controllers\Issue;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\IssueInitRequest;
use Illuminate\Support\Facades\DB;

const  ISSUER_PAGE_SIZE = 20;

class IssueController extends Controller
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
            $issuers = DB::table('dcuex_issuer_account')
                ->where('issuer_title_cn','like',"%$search%")
                ->orwhere('issuer_title_en','like',"%$search%")
                ->paginate(ISSUER_PAGE_SIZE);
        }else{
            $issuers = \DB::table('dcuex_issuer_account')
                ->paginate(ISSUER_PAGE_SIZE);
        }

        return view('issue.issuerIndex',['issuers' => $issuers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('issue.issuerCreate',['editFlag' => false,'issuerAccountEditFlag'=>true]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IssueInitRequest $request)
    {
        $issuer = $request->except(['_token','repeat_pwd','editFlag', 'issuerAccountEditFlag']);
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

        return view('issue.issuerCreate',[
            'editFlag' => true,
            'issuerAccountEditFlag' => false,
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
    public function update(IssueInitRequest $request, $id)
    {
        $updateIssue = $request->except(['_token', '_method','editFlag', 'issuerAccountEditFlag']);
        $updateIssue['updated_at'] = gmdate('Y-m-d H:i:s',time());
        $query = DB::table('dcuex_issuer_account')->where('id',$id);;
        if($query->first()){
            $query->update($updateIssue);
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
