<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\AdsRequest;
use App\Models\Cms\PortalAds;
use App\Traits\ImgCrop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 系统
 * Class AdsController
 * @package App\Http\Controllers\Cms
 */
class BannerController extends Controller
{
    use ImgCrop;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $search = trim($request->search,'');
        $orderC = trim($request->orderC,'');

        $banner = PortalAds::where('location', PortalAds::LOCATION_ONE)
            ->when($search, function ($query) use ($search){
                return $query->where('title','like',"%$search%");
            })
            ->when($orderC, function ($query) use ($orderC){
                return $query->orderBy('order', 'asc')->orderBy('created_at', $orderC);
            }, function ($query) {
                return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc'); //默认创建时间倒序
            })
            ->paginate(config('app.pageSize'));

        return view('cms.bannerIndex',compact('search', 'banner'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cms.bannerCreate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdsRequest $request)
    {
        $banner = $request->except(['_token','editFlag','files','x','y','w','h',
            $request->order?'':'order'
        ]);
        $banner['location'] = PortalAds::LOCATION_ONE;
        $banner['order'] =  $request->order?:1;
        $banner['created_at'] = gmdate('Y-m-d H:i:s',time());

        $fieldName = config('imgCrop.banner.name');
        $dir = config('imgCrop.banner.dir');

        if ($request->$fieldName) {
            $banner[$fieldName] = 'storage/'.$dir.'/'.$request->$fieldName;
            if ($request->hasFile($fieldName)) {
                $banner[$fieldName] = $request->file($fieldName)->store('public/payPath');
            }
        }

        if (PortalAds::create($banner)) {

            return redirect('portal/ads');
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
        $banner = PortalAds::findOrFail($id);

        return view('cms.bannerCreate',[
            'editFlag' => true,
            'banner' => $banner
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdsRequest $request, $id)
    {
        $banner = $request->except(['_token','_method','editFlag','files','x','y','w','h']);
        $banner['location'] = PortalAds::LOCATION_ONE;
        $banner['order'] =  $request->order?:1;
        $banner['updated_at'] = gmdate('Y-m-d H:i:s',time());

        $fieldName = config('imgCrop.banner.name');
        $dir = config('imgCrop.banner.dir');

        if ($request->$fieldName) {
            $banner[$fieldName] = 'storage/'.$dir.'/'.$request->$fieldName;
            if ($request->hasFile($fieldName)) {
                $banner[$fieldName] = $request->file($fieldName)->store('public/payPath');
            }
        }

        if (PortalAds::updateOrCreate(['id' => $id], $banner)) {

            return redirect('portal/ads');
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
        if (PortalAds::destroy($id)) {

            return response()->json([]);
        }
    }

    /**
     * 上传图片
     *
     * @param $dir
     */
    public function upload($dir)
    {
        $model = 'banner';

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
