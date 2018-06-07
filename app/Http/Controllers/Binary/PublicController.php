<?php

namespace App\Http\Controllers\Binary;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;


class PublicController extends Controller
{
    /**
     * 预览storage下公开文件
     *
     * @param $path // 文件路径
     * @return \Illuminate\Http\Response
     */
    private function fileResponse($path)
    {
        if(!File::exists($path)) abort(404);
        $type = File::mimeType($path);
        $file = File::get($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }


    /**
     * 显示storage/app/public 下币种 Icon
     *
     * @param $filename
     * @return \Illuminate\Http\Response
     */
    public function currencyIcon($filename)
    {
        return $this->fileResponse(config('file.currencyIconPath') . "/$filename");
    }

}
