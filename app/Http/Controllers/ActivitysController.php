<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\RActivitys;
use App\RCourses;
use DB;
use App\RActivityImgs; //废弃，使用Images
use App\Images;
use App\RMedals;
use App\LinkUAs;

class ActivitysController extends Controller
{
    /** 
     * 创建活动
     */
    public function doActivity(Request $request){
        if($request->has('title') && $request->has('desc') && $request->has('cover') && $request->has('meid')){
            try {
                DB::beginTransaction();
                    //保存封面图
                    $cover = $request->cover;
                    $cover['key'] = "activity-c";
                    $cover['key_id'] = 0;
                    $coverImg = new Images();
                    $coverImg->fillable(array_keys($cover));
                    $coverImg->fill($cover);
                    $coverImg->save();

                    //保存活动
                    $activity = new RActivitys();
                    $activity->fill([
                        'title' => $request->title,  //标题
                        'desc' => $request->desc,    //描述
                        'distance' => $request->distance,//完成条件
                        'cover' => $coverImg->id,    //封面图id
                        'meid' => $request->meid,    //勋章meid
                        'content' => $request->has('content') ? $request->content : "",  //内容
                        'period' => $request->has('period') ? $request->period : date('Y-m-d H:i:s', strtotime("+30 days"))  //截止时间
                    ]);
                    $activity->save();

                    //保存内容图片
                    $original = []; $thumbnail = [];
                    if($request->has('imgs')){
                        $i = 0;
                        foreach($request->imgs as $img){
                            $img['key'] = "activity";
                            $img['key_id'] = $activity->id;
    
                            $activityImg = new Images();
                            $activityImg->fillable(array_keys($img));
                            $activityImg->fill($img);
                            $activityImg->save();
    
                            $original[$i] = $img['original'];
                            $thumbnail[$i] = $img['thumbnail'];
                            $i++;
                        }
                    }
                DB::commit();
                // 返回数据
                $data = $activity;
                $data['acid'] = $activity->id; unset($data['id']); //修改id为acid，与数据库保持一致
                $data['imgs'] = [ $original, $thumbnail];
                $data['cover'] = [ //封面id返回替换为封面图片
                    'original' => $cover['original'],
                    'thumbnail' => $cover['thumbnail']
                ];
                $data['medal'] = RMedals::where('meid', $data->meid)->first(); unset($data['meid']); //勋章返回替换
                return returnData(true, "操作成功", $data);
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "标题、描述、封面图、勋章id缺一不可", null);
        }
    }

    /** 
     * 获取活动列表
     */
    public function getList(Request $request){
        $request->has('pageindex') ? $pageindex = $request->pageindex+1 : $pageindex = 1;  //当前页 1,2,3,...,首次查询可以传0
        $request->has('pagesize') ? $pagesize = $request->pagesize : $pagesize = 10;  //页面大小
        try {
            $activitys = RActivitys::orderBy('created_at', 'desc')
                                ->skip(($pageindex-1)*$pagesize)
                                ->take($pagesize)
                                ->get();
            $data = []; //动态联合数据
            for($n = 0; $n<count($activitys); $n++){
                $data[$n] = $activitys[$n];
                //获取封面图
                $cover = Images::where('id', $data[$n]['cover'])->first();
                $data[$n]['cover'] = [ //封面id返回替换为封面图片
                    'original' => $cover['original'],
                    'thumbnail' => $cover['thumbnail']
                ];
                 //获取勋章
                $data[$n]['medal'] = RMedals::where('meid', $data[$n]['meid'])->first();
            }
            //返回数据处理
            $re = [
                'pageindex' => $pageindex,
                'pagesize' => $pagesize,
                'activitys' => $data
            ];
            return returnData(true, "操作成功", $re);
        } catch (\Throwable $th) {
            return returnData(false, $th);
        }
    }

    /** 
     * 获取轮播活动
     */
    public function getSwipper(Request $request){
        try {
            $num = $request->has('num') ? $request->num : 3;
            $activitys = RActivitys::inRandomOrder()
                                // ->latest()   //ordered by the created_at column
                                ->limit($num)
                                ->select('acid', 'title', 'desc', 'cover')
                                ->get();
            $data = []; //动态联合数据
            for($n = 0; $n<count($activitys); $n++){
                $data[$n] = $activitys[$n];
                //获取封面图
                $cover = Images::where('id', $data[$n]['cover'])->first();
                $data[$n]['cover'] = [ //封面id返回替换为封面图片
                    'original' => $cover['original'],
                    'thumbnail' => $cover['thumbnail']
                ];
            }
            return returnData(true, "操作成功", $data);
        } catch (\Throwable $th) {
            return returnData(false, $th);
        }
    }

    /**
     * 获取活动详细
     */
    public function getDetail(Request $request){
        if($request->has('acid')){
            try {
                //获取活动
                $activity = RActivitys::where('acid', $request->acid)->get()[0]; // 注意：单一数据存在数组中0
                //获取图片
                $imgs = Images::where('key', 'activity')->where('key_id', $request->acid)->get();
                $original = []; $thumbnail = [];
                foreach($imgs as $img){
                    $original []= $img->original;
                    $thumbnail []= $img->thumbnail;
                }
                $activity['imgs'] = [ 
                    'original' => $original,
                    'thumbnail' => $thumbnail
                ];
                //获取封面图
                $cover = Images::where('id',$activity['cover'])->first();
                $activity['cover'] = [ //封面id返回替换为封面图片
                    'original' => $cover['original'],
                    'thumbnail' => $cover['thumbnail']
                ];
                 //获取勋章
                 $activity['medal'] = RMedals::where('meid', $activity['meid'])->first();
                return returnData(true, "操作成功", $activity);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺少acid", null);
        }
    }

    /** 
     * 创建课程
     */
    public function doCourse(Request $request){
        if($request->has('title') && $request->has('text') && $request->has('img')){
            $course = new RCourses();
            $course->fill([
                'title' => $request->title,  //课程标题
                'text' => $request->text,    //课程内容
            ]);
            try {
                DB::beginTransaction();
                    $course->save();
                    $img = $request->img;
                    //保存封面图
                    $img['key'] = "course";
                    $img['key_id'] = $course->id;
                    $courseImg = new Images();
                    $courseImg->fillable(array_keys($img));
                    $courseImg->fill($img);
                    $courseImg->save();
                    // 返回数据
                    $data = $course;
                    $data['rcid'] = $course->id; unset($data['id']); //修改id为rcid，与数据库保持一致
                    $data['img'] = [
                        'original' => $img['original'],
                        'thumbnail' => $img['thumbnail']
                    ];
                DB::commit();
                return returnData(true, "操作成功", $data);
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺少标题、内容或者封面图", null);
        }
    }

    /** 
     * 获取课程列表
     */
    public function getCourses(Request $request){
        $request->has('num') ? $num = $request->num : $num = 2;
        try {
            $courses = RCourses::orderBy('created_at', 'desc')
                                ->select('rcid', 'title')
                                ->take($num)
                                ->get();
            $data = []; //联合数据
            for($n = 0; $n<count($courses); $n++){
                $data[$n] = $courses[$n];
                //获取图片
                $img = Images::where('key', 'course')->where('key_id', $data[$n]['rcid'])->first();
                $data[$n]['img'] = [
                    'original' => $img->original,
                    'thumbnail' => $img->thumbnail
                ];
            }
            return returnData(true, "操作成功", $data);
        } catch (\Throwable $th) {
            return returnData(false, $th);
        }
    }
    
    /** 
     * 获取课程详细
     */
    public function getCourseDetail(Request $request){
        if($request->has('rcid')){
            try {
                $course = RCourses::where('rcid', $request->rcid)->first();
                //获取图片
                $img = Images::where('key', 'course')->where('key_id', $course['rcid'])->first();
                $course['img'] = [
                    'original' => $img->original,
                    'thumbnail' => $img->thumbnail
                ];
                return returnData(true, "操作成功", $course);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺少课程rcid");
        }
    }

    /** 
     * 报名参加活动
     */
    public function signActivity(Request $request){
        if($request->has('rid') && $request->has('acid')){
            $signlog = LinkUAs::where('rid', $request->rid)->where('acid', $request->acid)->get();
            if(count($signlog)==0){
                $sign = new LinkUAs();
                $sign->fill($request->all());
                try {
                    DB::beginTransaction();
                        $sign->save();
                    DB::commit();
                    unset($sign['id']);
                    return returnData(true, "报名成功", $sign);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return returnData(false, $th);
                }
            }else{
                return returnData(false, "您已经报名过了", $signlog);
            }
        }else{
            return returnData(false, "缺失rid或acid", null);
        }
    }

    /** 
     * 查询用户是否已报名
     */
    public function signActivityCheck(Request $request){
        if($request->has('rid') && $request->has('acid')){
            $signlog = LinkUAs::where('rid', $request->rid)->where('acid', $request->acid)->first();
            if($signlog){
                return returnData(true, "已报名", $signlog);
            }else{
                return returnData(false, "未报名", null);
            }
        }else{
            return returnData(false, "缺失rid或acid", null);
        }
    }

    /** 
     * 获取已报名人数
     */
    public function getSignNum(Request $request){
        if($request->has('acid')){
            try {
                $signlog = LinkUAs::where('acid', $request->acid)->get();
                return returnData(true, "操作成功", count($signlog));
            } catch (\Throwable $th) {
                return returnData(false, "操作失败", $th);
            }
        }else{
            return returnData(false, "缺少acid", null);
        }
    }
}
