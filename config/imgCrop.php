<?php

return [

   /*
  |--------------------------------------------------------------------------
  | Announcement-Cover  Image Crop config 公告通知封面 256*144
  |--------------------------------------------------------------------------
  |
  | Announcement-Cover cropping related configuration information.
  | upload 允许的尺寸   preview 预览区的尺寸   crop 裁剪的尺寸  dir 存储目录名  name 字段名.
  |
  */

    'announcement' => [
        'upload' => [
            'max_width'  => 600,
            'max_height' => 338,
            'min_width'  => 256,
            'min_height' => 144,
        ],

        'preview' => [
            'upload_width' => 350,
            'crop_width'   => 150,
            'crop_height'  => 84,
        ],

        'crop' => [
            'width'  => 256,
            'height' => 144,
        ],

        'scale' => 0.6,
        'dir'   => 'annoCover',
        'route' => 'anno/cover/',
        'name'  => 'cover'
    ],

    /*
   |--------------------------------------------------------------------------
   | Banner Crop config 首页banner 1920*473
   |--------------------------------------------------------------------------
   |
   | Banner cropping related configuration information.
   | upload 允许的尺寸   preview 预览区的尺寸   crop 裁剪的尺寸  dir 存储目录名  name 字段名.
   |
   */
    'banner' => [
        'upload' => [
            'max_width'  => 2200,
            'max_height' => 542,
            'min_width'  => 1920,
            'min_height' => 473,
        ],

        'preview' => [
            'upload_width' => 400,
            'crop_width'   => 220,
            'crop_height'  => 54,
        ],

        'crop' => [
            'width'  => 1920,
            'height' => 473,
        ],

        'scale' => 0.6,
        'dir'   => 'portalImg',
        'route' => 'portal/banner/',
        'name'  => 'cover'
    ],

];
