<?php

namespace App\Traits;

use Intervention\Image\ImageManagerStatic;
use App\libraries\UploadHandlerForPublic;
use Illuminate\Support\Facades\Input;

trait ImgCrop {

    /**
     * 图片上传
     * @param $dir
     */
    public function imgUpload($dir)
    {
        $option = array(
            //配置上传路径
            'upload_dir' => storage_path($dir) . '/',
            'upload_url' => storage_path($dir) . '/',
            'image_versions' =>['' =>['auto_orient' => true]], // 不添加缩略图
        );

        new UploadHandlerForPublic($option);
    }


    /**
     * 图片剪裁
     * @param $width
     * @param $height
     * @param $dir 上传图片存储位置
     * @param $imgName  上传图片时ajax携带的图片参数
     * @return \Illuminate\Http\JsonResponse
     */
    public function imgCrop($width, $height, $dir, $imgName)
    {
        //图片最终保存的宽高
        $height = intval(Input::get($height));
        $width = intval(Input::get($width));

        //图片加载路径
        $imagePathBase = Input::get($imgName);
        $imageLoadPath = storage_path($dir) . "/$imagePathBase";

        //页面内原始图片预览区宽度，默认600
        $orgImgWidth = 600;
        $getImgWidth = Input::get('imgWidth');
        if($getImgWidth){
            $orgImgWidth = intval($getImgWidth);
        }

        $img = ImageManagerStatic::make($imageLoadPath);

        //将裁剪坐标按原始图片大小进行缩放
        $imgWidth = $img->width();
        $scale = $imgWidth/$orgImgWidth;
        $w = intval(Input::get('w')*$scale);
        $h = intval(Input::get('h')*$scale);
        //规定裁剪后图片大小
        $x = intval(Input::get('x')*$scale);
        $y = intval(Input::get('y')*$scale);

        // 裁剪图片，并将裁剪后的图片大小重设为规定大小
        $img->crop($w, $h, $x, $y)->resize($width, $height);

        // 保存图片
        $img->save($imageLoadPath);

        return response()->json(['url' => $imagePathBase]);
    }
} 