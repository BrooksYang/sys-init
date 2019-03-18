<?php

namespace App\Http\Controllers\Cms;

use App\Models\Cms\PortalAds;
use App\Traits\ImgCrop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Banner图管理-Wap端
 *
 * Class BannerWapController
 * @package App\Http\Controllers\Cms
 */
class BannerWapController extends Controller
{
    use ImgCrop;

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
        $banner = PortalAds::findOrFail($id);

        return view('cms.bannerWapCreate',[
            'editFlag' => true,
            'bannerWap' => $banner
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
        $banner = $request->except(['_token','_method','editFlag','files','x','y','w','h']);
        $banner['updated_at'] = self::carbonNow();

        $fieldName = config('imgCrop.bannerWap.name');
        $dir = config('imgCrop.bannerWap.dir');

        if ($request->$fieldName) {
            $banner[$fieldName] = 'storage/'.$dir.'/'.$request->$fieldName;
            if ($request->hasFile($fieldName)) {
                $banner[$fieldName] = $request->file($fieldName)->store("public/$dir");
            }
        }

        if (PortalAds::updateOrCreate(['id' => $id], $banner)) {

            return redirect('portal/ads');
        }
    }

    /**
     * 上传图片
     *
     * @param $dir
     */
    public function upload($dir)
    {
        $model = 'bannerWap';

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
