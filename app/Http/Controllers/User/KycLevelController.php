<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\KycLevelRequest;
use App\Models\KycLevel;
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
    public function index()
    {
        //
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
        //
    }
}
