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
   | Banner Crop config 首页banner 1920*650
   |--------------------------------------------------------------------------
   |
   | Banner cropping related configuration information.
   | upload 允许的尺寸   preview 预览区的尺寸   crop 裁剪的尺寸  dir 存储目录名  name 字段名.
   |
   */
    'banner' => [
        'upload' => [
            'max_width'  => 2200,
            'max_height' => 745,
            'min_width'  => 1920,
            'min_height' => 650,
        ],

        'preview' => [
            'upload_width' => 420,
            'crop_width'   => 220,
            'crop_height'  => 74,
        ],

        'crop' => [
            'width'  => 1920,
            'height' => 650,
        ],

        'scale' => 0.6,
        'dir'   => 'portalImg',
        'route' => 'portal/banner/',
        'name'  => 'cover'
    ],


   /*
   |--------------------------------------------------------------------------
   | Banner-Wap Crop config 首页banner - Wap端  750*490
   |--------------------------------------------------------------------------
   |
   | Banner-Wap cropping related configuration information.
   | upload 允许的尺寸   preview 预览区的尺寸   crop 裁剪的尺寸  dir 存储目录名  name 字段名.
   |
   */
    'bannerWap' => [
        'upload' => [
            'max_width'  => 950,
            'max_height' => 621,
            'min_width'  => 750,
            'min_height' => 490,
        ],

        'preview' => [
            'upload_width' => 420,
            'crop_width'   => 220,
            'crop_height'  => 144,
        ],

        'crop' => [
            'width'  => 750,
            'height' => 490,
        ],

        'scale' => 0.6,
        'dir'   => 'portalImg',
        'route' => 'portal/banner/wap/',
        'name'  => 'cover_wap'
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo Crop config 首页logo 35*35
    |--------------------------------------------------------------------------
    |
    | Logo cropping related configuration information.
    | upload 允许的尺寸   preview 预览区的尺寸   crop 裁剪的尺寸  dir 存储目录名  name 字段名.
    |
    */
    'portalConf' => [
        'upload' => [
            'max_width'  => 150,
            'max_height' => 150,
            'min_width'  => 35,
            'min_height' => 35,
        ],

        'preview' => [
            'upload_width' => 120,
            'crop_width'   => 60,
            'crop_height'  => 60,
        ],

        'crop' => [
            'width'  => 35,
            'height' => 35,
        ],

        'scale' => 1,
        'dir'   => 'portalImg',
        'route' => 'portal/logo/',
        'name'  => 'logo'
    ],

    /*
   |--------------------------------------------------------------------------
   | Legal Currency-Flag 58*35 国旗  宽度58px -1.657
   |--------------------------------------------------------------------------
   |
   | Legal Currency-Flag cropping related configuration information.
   | upload 允许的尺寸   preview 预览区的尺寸   crop 裁剪的尺寸..
   |
   */

    'flag' => [
        'upload' => [
            'max_width'  => 0,
            'max_height' => 0,
            'min_width'  => 58,
            'min_height' => 35,
        ],

        'preview' => [
            'upload_width' => 174,
            'crop_width'   => 116,
            'crop_height'  => 70,
        ],

        'crop' => [
            'width'  => 58,
            'height' => 35,
        ],

        'scale' => 0.9,

        'zoom'  => 2,

        'dir'   => 'app/public/flag',

        'name' => 'flag'
    ],

];
