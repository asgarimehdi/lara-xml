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
    return view('welcome');
});
Route::get('xml','xmlController@index');
Route::get('show','xmlController@show');
Route::get('save','xmlController@save');
Route::get('remote','xmlController@remote');
Route::get('agent','xmlController@userAgent');
Route::get('childxml','xmlController@fetchChildXmlFromSitemap');
Route::get('validate','xmlController@isUrlValid');
