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

Route::get('/weixin','Weixin\IndexController@index');
Route::post('/weixin','Weixin\IndexController@index');

//创建微信订阅号自定义菜单
Route::get('/weixinMenunCreate','Weixin\ApiController@create');

//操作系统

//单词系统
Route::get('/wordList','Weixin\WordController@wordList');
Route::get('/getMoxieWord','Weixin\WordController@getMoxieWord');
Route::get('/word/decodeWeChatUserInfo','Weixin\WordController@decodeWeChatUserInfo');
Route::get('/word/meGetWordList','Weixin\WordController@meGetWordList');
Route::post('/word/submitFunDiy','Weixin\WordController@submitFunDiy');
Route::post('/word/submitMeMoxieGroup','Weixin\WordController@submitMeMoxieGroup');
Route::post('/word/getMeMoxieList','Weixin\WordController@getMeMoxieList');
Route::get('/word/getClientInfo','Weixin\WordController@getClientInfo');
Route::get('/word/checkToken','Weixin\WordController@checkToken');
Route::get('/word/uniformSend','Weixin\WordController@uniformSend');
Route::get('/test','Weixin\WordController@test');
//

//QueryList
Route::get('/querylist/index','QueryList\IndexController@index');
Route::get('/querylist/caijiView','QueryList\IndexController@caijiView');
Route::post('/querylist/caiji','QueryList\IndexController@caiji');
Route::get('/querylist/test','QueryList\TestController@index');
Route::post('/querylist/saveCaiji','QueryList\IndexController@saveCaiji');
Route::get('/querylist/getCaijiUrlList','QueryList\IndexController@getCaijiUrlList');

Route::get('/querylist/crontabStart','QueryList\IndexController@crontabStart');
