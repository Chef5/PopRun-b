<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
/**
 * 全局接口
 */
Route::prefix('main')->group(function () {
    //获取token
    Route::get('/getToken', function(Request $request){
        return var_dump($request);
    });
    //用户授权注册
    Route::post('/wxAuth', 'RUsersController@index');
});

/**
 * 跑步相关
 */
Route::prefix('run')->group(function () {
    Route::get('/', function(){
        return "跑步接口暂未开发完成";
    });
});

/**
 * 动态圈子
 */
Route::prefix('moments')->group(function () {
    Route::get('/', function(){
        return "动态接口暂未开发完成";
    });
});

/**
 * 活动广场
 */
Route::prefix('pub')->group(function () {
    Route::get('/', function(){
        return "活动接口暂未开发完成";
    });
});

/**
 * 个人中心
 */
Route::prefix('user')->group(function () {
    Route::get('/', function(){
        return "用户接口暂未开发完成";
    })->middleware('userAuth');
});

/**
 * 管理接口
 */
Route::prefix('admin')->group(function () {
    Route::get('/', function(){
        return "管理接口暂未开发完成";
    });
});