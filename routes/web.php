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
Route::get('/home', 'HomeController@index')->middleware(['web', 'auth:admin']);