<?php

namespace App\Http\Controllers\Cms;

use App\Models\Cms\TokenApply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 上币申请
 * Class TokenApplyController
 * @package App\Http\Controllers\Cms
 */
class TokenApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'desc');

        $application = TokenApply::when($search, function ($query) use ($search){
                return $query->where('contact','like',"%$search%")
                    ->orWhere('project', 'like', "%$search%");
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('order', 'asc')->orderBy('created_at', $orderC);
            })
            ->paginate(config('app.pageSize'));

        return view('cms.tokenApplyIndex',compact('search', 'application'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (TokenApply::destroy($id)) {

            return response()->json([]);
        }
    }
}
