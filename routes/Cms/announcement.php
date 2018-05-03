<?php

/*
|--------------------------------------------------------------------------
| Cms announcement Routes
|--------------------------------------------------------------------------
|
| CMS 公告管理路由
|
*/


Route::group(['middleware' => ['web', 'auth:admin']], function()
{
    //公告管理
    Route::resource('cms/announcement', 'Cms\AnnouncementController');

    //更新公告状态-组合更新公告的草稿/发布状态，置顶/取消置顶状态
    Route::patch('cms/announcement/updateStatus/{anno}', 'Cms\AnnouncementController@updateStatus');

});




