<?php

/*
|--------------------------------------------------------------------------
| Content management system Routes
|--------------------------------------------------------------------------
|
| CMS 内容管理路由
|
*/

/**
 * 系统公告
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen', 'mongo.log']], function()
{
    //公告管理
    Route::resource('cms/announcement', 'Cms\AnnouncementController');

    //更新公告状态-组合更新公告的草稿/发布状态，置顶/取消置顶状态
    Route::patch('cms/announcement/updateStatus/{anno}', 'Cms\AnnouncementController@updateStatus');

});


/**
 * 系统 FAQ 文档
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    //FAQ类型管理
    Route::resource('faq/type', 'Cms\FaqTypeController');

    //FAQ管理
    Route::resource('faq/manage', 'Cms\FaqController');

    //更新FAQ文档状态-草稿/发布状态
    Route::patch('faq/manage/updateStatus/{faq}', 'Cms\FaqController@updateStatus');
});

/**
 * 系统门户配置
 *
 */
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function()
{
    // 系统广告位管理
    Route::resource('portal/ads', 'Cms\BannerController');

    // 系统广告位管理- Wap端
    Route::resource('portal/ads/wap', 'Cms\BannerWapController', ['only' => ['edit', 'update']]);

    // 门户基本信息配置
    Route::resource('portal/conf', 'Cms\PortalConfController',['except' => ['index','show','destroy']]);

    // 门户上币申请
    Route::resource('portal/token/apply', 'Cms\TokenApplyController',['only' => ['index','destroy']]);
});

