<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('home');
})->middleware(['web', 'auth:admin']);

Route::get('getSysBalance', function () {
   $etherScan =  \App\User::getSysWithDrawAddrBalance(config('blockChain.sys_withdraw_addr'));
   dd($etherScan);
})->middleware(['web', 'auth:admin']);

//代币 icon显示路由
Route::get('currencyIcon/{filename}','Binary\PublicController@currencyIcon');

//Dashboard
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen','mongo.log']], function () {
    Route::get('/home', 'HomeController@index');
    Route::get('/demo', 'HomeController@index');
});

//Auth-logout
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

//LockScreen and Unlock
Route::group(['middleware' => ['web','auth:admin']], function () {
    Route::get('/lockScreen', 'Auth\LockScreenController@lockScreen');
    Route::post('/lockScreen', 'Auth\LockScreenController@unlock');
});

/*
|--------------------------------------------------------------------------
| 图片处理
|--------------------------------------------------------------------------
| 图片上传和裁剪
|
*/
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen']], function ()
{
    // 公告封面上传和裁剪
    Route::post('anno/cover/upload/{dir}', 'Cms\AnnouncementController@upload');
    Route::patch('anno/cover/upload/{dir}', 'Cms\AnnouncementController@upload');
    Route::get('anno/cover/crop/{dir}', 'Cms\AnnouncementController@crop');

    // 首页Banner上传和裁剪
    Route::post('portal/banner/upload/{dir}', 'Cms\BannerController@upload');
    Route::patch('portal/banner/upload/{dir}', 'Cms\BannerController@upload');
    Route::get('portal/banner/crop/{dir}', 'Cms\BannerController@crop');

    // 首页Banner上传和裁剪- Wap端
    Route::post('portal/banner/wap/upload/{dir}', 'Cms\BannerWapController@upload');
    Route::patch('portal/banner/wap/upload/{dir}', 'Cms\BannerWapController@upload');
    Route::get('portal/banner/wap/crop/{dir}', 'Cms\BannerWapController@crop');

    // 首页Logo上传和裁剪
    Route::post('portal/logo/upload/{dir}', 'Cms\PortalConfController@upload');
    Route::patch('portal/logo/upload/{dir}', 'Cms\PortalConfController@upload');
    Route::get('portal/logo/crop/{dir}', 'Cms\PortalConfController@crop');

    // 法币国旗图标上传和裁剪
    Route::post('currency/flag/upload/{dir}', 'OtcLegalCurrency\LegalCurrencyController@upload');
    Route::patch('currency/flag/upload/{dir}', 'OtcLegalCurrency\LegalCurrencyController@upload');
    Route::get('currency/flag/crop/{dir}', 'OtcLegalCurrency\LegalCurrencyController@crop');

});
