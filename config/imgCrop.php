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


];
