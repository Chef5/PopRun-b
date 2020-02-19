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
    //获取用户openid
    Route::post('/getOpenid', 'PublicController@getOpenid');
    //用户授权注册
    Route::post('/wxAuth', 'RUsersController@regster');
    // 图片上传
    Route::post('/uploadImg', 'PublicController@uploadImg');
    // 获取称号列表
    Route::get('/getHonorAll', 'PublicController@getHonorAll');
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
    // 发布动态
    Route::post('/doMoment', 'MomentsController@doMoment');
    // 删除动态
    Route::post('/delMoment', 'MomentsController@delMoment');
    // 发表评论
    Route::post('/doComment', 'MomentsController@doComment');
    // 点赞
    Route::post('/doLike', 'MomentsController@doLike');
    // 取消点赞
    Route::post('/doDislike', 'MomentsController@doDislike');
    // 获取个人动态
    Route::post('/getMine', 'MomentsController@getMine');
    // 获取动态
    Route::post('/getMoments', 'MomentsController@getMoments');
    // 获取某条动态
    Route::get('/getMomentById', 'MomentsController@getMomentById');
});

/**
 * 活动广场
 */
Route::prefix('pub')->group(function () {
    Route::get('/', function(){
        return "活动接口暂未开发完成";
    });
    // 创建活动
    Route::post('/doActivity', 'ActivitysController@doActivity');
    // 获取活动列表
    Route::post('/getList', 'ActivitysController@getList');
    // 获取轮播活动
    Route::get('/getSwipper', 'ActivitysController@getSwipper');
    // 获取轮播活动详细
    Route::get('/getSwipperDetail', 'ActivitysController@getSwipperDetail');
});

/**
 * 个人中心
 */
Route::prefix('user')->group(function () {
    Route::get('/', function(){
        return "token认证暂未开发完成";
    })->middleware('userAuth');
    //获取个人信息
    Route::post('/getUser', 'RUsersController@getUser');
    //修改个人信息
    Route::post('/doUpdate', 'RUsersController@doUpdate');
    // 注销账号
    Route::post('/doUnset', 'RUsersController@doUnset');
});

/**
 * 管理接口
 */
Route::prefix('admin')->group(function () {
    Route::get('/', function(){
        return "管理接口暂未开发完成";
    });
    // 初始化数据
    Route::get('/initData', 'AdminController@initData');
});