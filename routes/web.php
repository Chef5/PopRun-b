<?php
/*
 * @Description: Do not edit
 * @Company: TRs
 * @Date: 2020-05-01 15:53:02
 * @LastEditors: lym
 */

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

Route::get('/addActivity', function () {
    return view('addActivity');
});
Route::get('/addCourse', function () {
    return view('addCourse');
});
Auth::routes();
