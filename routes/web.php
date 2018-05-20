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

//代币 icon显示路由
Route::get('currencyIcon/{filename}','Binary\PublicController@currencyIcon');

//Dashboard
Route::group(['middleware' => ['web', 'auth:admin', 'lock.screen']], function () {
    Route::get('/home', 'HomeController@index');
    Route::get('/demo', 'HomeController@index');
});

//LockScreen and Unlock
Route::group(['middleware' => ['web','auth:admin']], function () {
    Route::get('/lockScreen', 'Auth\LockScreenController@lockScreen');
    Route::post('/lockScreen', 'Auth\LockScreenController@unlock');
});


