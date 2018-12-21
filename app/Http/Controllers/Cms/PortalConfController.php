<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\PortalConfRequest;
use App\Models\Cms\PortalConf;
use App\Traits\ImgCrop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PortalConfController extends Controller
{
    use ImgCrop;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $portalConf = PortalConf::find(1);
        if ($portalConf) {
            return redirect('portal/conf/1/edit');
        }

        return view('cms.portalConf');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortalConfRequest $request)
    {
        $portalConf = $request->except(['_token','editFlag','files','x','y','w','h']);
        $portalConf['created_at'] = gmdate('Y-m-d H:i:s',time());

        $fieldName = config('imgCrop.portalConf.name');
        $dir = config('imgCrop.portalConf.dir');

        if ($request->$fieldName) {
            $portalConf[$fieldName] = 'storage/'.$dir.'/'.$request->$fieldName;
            if ($request->hasFile($fieldName)) {
                $portalConf[$fieldName] = $request->file($fieldName)->store('public/payPath');
            }
        }
        //dd($portalConf);
        if (PortalConf::updateOrCreate(['id' => 1],$portalConf)) {

            return redirect('portal/conf/1/edit');
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
        $portalConf = PortalConf::findOrFail($id);

        return view('cms.portalConf',[
            'editFlag' => true,
            'portalConf' => $portalConf
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PortalConfRequest $request, $id)
    {
        $portalConf = $request->except(['_token','_method','editFlag','files','x','y','w','h']);
        $portalConf['updated_at'] = gmdate('Y-m-d H:i:s',time());

        $fieldName = config('imgCrop.portalConf.name');
        $dir = config('imgCrop.portalConf.dir');

        if ($request->$fieldName) {
            $portalConf[$fieldName] = 'storage/'.$dir.'/'.$request->$fieldName;
            if ($request->hasFile($fieldName)) {
                $portalConf[$fieldName] = $request->file($fieldName)->store('public/payPath');
            }
        }

        if (PortalConf::updateOrCreate(['id' => $id], $portalConf)) {

            return redirect('portal/conf/1/edit');
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
        //
    }

    /**
     * 上传图片
     *
     * @param $dir
     */
    public function upload($dir)
    {
        $model = 'portalConf';

        $this->imgUpload(
            base64_decode($dir),
            config("imgCrop.$model.upload.max_width"),
            config("imgCrop.$model.upload.max_height"),
            config("imgCrop.$model.upload.min_width"),
            config("imgCrop.$model.upload.min_height")
        );
    }

    /**
     * 裁剪图片
     *
     * @param $dir
     * @return \Illuminate\Http\JsonResponse
     */
    public function crop($dir)
    {
        return $this->imgCrop('width', 'height','imageUploadPreviewWidth', base64_decode($dir), 'cropImg');
    }

}
