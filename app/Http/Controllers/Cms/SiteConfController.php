<?php

namespace App\Http\Controllers\Cms;

use App\Models\Cms\PortalConf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SiteConfController extends Controller
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
        if ($id) {
            $configs = PortalConf::get([
                'currency_intro','rate','privacy_policy','token_apply_intro','disclaimer','about_us'
            ])->first()->toArray();
        }

        return view('cms.siteConf', [
            'editFlag' => true,
            'configs' => $configs ?? []
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
        $updateConfig = $request->except(['_token', '_method', 'editFlag']);

        Validator::make($request->all(),[
            'currency_intro'    => 'required',
            'rate'              => 'required',
            'privacy_policy'    => 'required',
            'token_apply_intro' => 'required',
            'disclaimer'        => 'required',
            'about_us'          => 'required',
        ])->validate();

        $updateConfig = $updateConfig + [ 'updated_at' => gmdate('Y-m-d H:i:s',time())];
        PortalConf::where('id',$id)->update($updateConfig);

        return redirect("portal/siteConf/$id/edit");
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
