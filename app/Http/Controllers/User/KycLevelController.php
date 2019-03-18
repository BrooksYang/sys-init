<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\KycLevelRequest;
use App\Models\KycLevel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * KYC认证等级管理
 * Class KycLevelController
 * @package App\Http\Controllers\User
 */
class KycLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search','');

        $kycLevel = KycLevel::when($search, function ($query) use ($search) {
                $query->where('name','like',"%$search%");
            })->orderBy('level','asc')
            ->paginate(config('app.pageSize'));

        return view('user.kycLevelIndex', compact('kycLevel','search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.kycLevelCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  KycLevelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(KycLevelRequest $request)
    {
        $kycLevel = $request->except(['_token','editFlag']);

        $kycLevel = KycLevel::create($kycLevel);

        return redirect('user/kycLevel/manage');
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
        $kycLevel = KycLevel::findOrFail($id);
        $editFlag = true;

        return view('user.kycLevelCreate', compact('kycLevel','editFlag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  KycLevelRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(KycLevelRequest $request, $id)
    {
        $kycLevel = $request->except(['_token', '_method', 'editFlag']);
        $kycLevel['updated_at'] = self::carbonNow();

        $res = KycLevel::updateOrCreate(['id' => $id],$kycLevel);

        return redirect('user/kycLevel/manage');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (User::where('kyc_level_id', $id)->exists()) {
            return response()->json(['code' => 100030 ,'error' => '该等级已被使用暂不能删除']);
        }

        if (KycLevel::destroy($id)) {
            return response()->json([]);
        }
    }
}
