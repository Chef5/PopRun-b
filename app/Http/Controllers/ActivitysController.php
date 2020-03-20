<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\RActivitys;
use App\RActivityImgs;

class ActivitysController extends Controller
{
    //创建活动
    public function doActivity(Request $request){
        if($request->has('rid')){
            if($request->has('title') && $request->has('desc')){
                $activity = new RActivitys();
                $activity->fill([
                    'rid' => $request->rid,
                    'title' => $request->title,  //标题
                    'desc' => $request->desc,    //描述
                    'cover' => $request->has('cover') ? $request->cover : 0,  //取第几张图片为封面图，默认第一张
                    'content' => $request->has('content') ? $request->content : "",  //内容
                    'period' => $request->has('period') ? $request->period : strtotime("+30 days")  //有效时间
                ]);
                if($activity->save()){
                    $original = []; $thumbnail = []; $i = 0;
                    foreach($request->imgs as $img){
                        $img['acid'] = $activity->id;
                        $activityImg = new RActivityImgs();
                        $activityImg->fillable(array_keys($img));
                        $activityImg->fill($img);
                        $activityImg->save();

                        $original[$i]['url'] = $img['original'];
                        $original[$i]['width'] = $img['width'];
                        $original[$i]['height'] = $img['height'];
                        $thumbnail[$i]['url'] = $img['thumbnail'];
                        $thumbnail[$i]['width'] = $img['mwidth'];
                        $thumbnail[$i]['height'] = $img['mheight'];
                        $i++;
                    }
                    // 返回数据
                    $data = $activity;
                    $data['acid'] = $activity->id; unset($data['id']); //修改id为moid，与数据库保持一致
                    $data['imgs'] = [
                        'original' => $original,
                        'thumbnail' => $thumbnail
                    ];
                    return returnData(true, "操作成功", $data);
                }else{
                    return returnData(false, "保存失败", null);
                }
            }else{
                return returnData(false, "标题、描述缺一不可", null);
            }
        }else{
            return returnData(false, "缺rid", null);
        }
    }

    //获取活动列表
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
                //获取图片
                $imgs = RActivityImgs::where('acid', $activitys[$n]['acid'])->get();
                $original = []; $thumbnail = [];
                foreach($imgs as $img){
                    $original []= [
                        "url" => $img->original,
                        "width" => $img->width,
                        "height" => $img->height
                    ];
                    $thumbnail []= [
                        "url" => $img->thumbnail,
                        "width" => $img->mwidth,
                        "height" => $img->mheight
                    ];
                }
                $data[$n]['imgs'] = [
                    'original' => $original,
                    'thumbnail' => $thumbnail
                ];
            }
            //返回数据处理
            $re = [
                'pageindex' => $pageindex,
                'pagesize' => $pagesize,
                'activitys' => $data
            ];
            return returnData(true, "操作成功", $re);
        } catch (\Throwable $th) {
            returnData(false, $th);
        }
    }

    //获取轮播活动
    public function getSwipper(Request $request){
        try {
            $activitys = RActivitys::inRandomOrder()
                                // ->latest()   //ordered by the created_at column
                                ->limit(3)
                                ->select('acid', 'title', 'desc', 'cover')
                                ->get();
            $data = []; //动态联合数据
            for($n = 0; $n<count($activitys); $n++){
                $data[$n] = $activitys[$n];
                //获取封面图片
                $imgs = RActivityImgs::where('acid', $activitys[$n]['acid'])->get();
                $cover['original'] = $imgs[$activitys[$n]['cover']]->original;
                $cover['thumbnail'] = $imgs[$activitys[$n]['cover']]->thumbnail;
                $data[$n]['cover'] = $cover;
            }
            return returnData(true, "操作成功", $data);
        } catch (\Throwable $th) {
            returnData(false, $th);
        }
    }

    //获取轮播活动详细
    public function getSwipperDetail(Request $request){
        if($request->has('acid')){
            try {
                //获取活动
                $activity = RActivitys::where('acid', $request->acid)->get()[0]; // 注意：单一数据存在数组中0
                //获取图片
                $imgs = RActivityImgs::where('acid', $request->acid)->get();
                $activity['imgs'] = $imgs;
                return returnData(true, "操作成功", $activity);
            } catch (\Throwable $th) {
                returnData(false, $th);
            }
        }else{
            return returnData(false, "缺少acid", null);
        }
    }
}
