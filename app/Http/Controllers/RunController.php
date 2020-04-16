<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\RRuns;
use App\Hitokoto;
use App\Images;
use App\RMoments;
use App\RUsers;

class RunController extends Controller
{
    /** 
     * 获取随机一言
     */
    public function getHitokoto(Request $request){
        if($request->has('type')) $url = 'http://v1.alapi.cn/api/hitokoto?format=json&type='.$request->type;
        else $url = 'http://v1.alapi.cn/api/hitokoto?format=json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = [];
        for($i=0; $i<10; $i++){
            $returnjson=curl_exec($curl);
            if($returnjson){
                //整理返回数据
                $json = json_decode($returnjson);
                if($json->code == 200){
                    $hitokoto = new Hitokoto();
                    $hitokoto['hitokoto'] = $json->data->hitokoto;
                    $hitokoto['type'] = $json->data->type;
                    $hitokoto['from'] = $json->data->from;
                    $hitokoto['creator'] = $json->data->creator;
                    $hitokoto->save();
                    $json->data->id = $hitokoto->id;
                    array_push($data, $json->data);
                    // return returnData(true, "操作成功", $json->data);
                    if(count($data) == 5) break;
                }
                // else{
                //     return returnData(false, $json);
                // }
            }
            // else{
            //     return returnData(false, curl_error($curl));
            // }
        }
        return returnData(true, "操作成功", $data);
    }

    /**  
     * 跑步开始
     */
    public function doStart(Request $request){
        if($request->has('rid')){
            $run = new RRuns();
            try {
                DB::beginTransaction();
                    // 跑步初始数据
                    $run->fillable(array_keys($request->all()));
                    $run->fill($request->all());
                    $run->save();
                DB::commit();
                // 处理返回数据
                $data = RRuns::where('ruid', $run->id)->first();
                return returnData(true, '操作成功', $data);
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少rid');
        }
    }

    /**  
     * 跑步结束
     */
    public function doEnd(Request $request){
        if($request->has('ruid')){
            $run = null;
            if($request->has('distance') && 
                $request->has('calorie') && 
                $request->has('speed_top') && 
                $request->has('speed_low') && 
                $request->has('speed') && 
                $request->has('time_end') && 
                $request->has('time_run')&& 
                $request->has('latitude_end')&& 
                $request->has('longitude_end'))
            {
                try {
                    DB::beginTransaction();
                        $run = RRuns::where('ruid', $request->ruid)->update($request->all());
                        if($request->has('img')){
                            $image = new Images();
                            $img = $request->img;
                            $img['key'] = 'run';
                            $img['key_id'] = $request->ruid;
                            $image->fill($img);
                            $image->save();
                        }
                    DB::commit();
                    return returnData(true, '操作成功', RRuns::where('ruid', $request->ruid)->first());
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return returnData(false, $th);
                }
            }else{
                return returnData(false, '缺少必须参数，已传参数见data', array_keys($request->all()));
            }
        }else if($request->has('rid')){
            $run = new RRuns();
            try {
                DB::beginTransaction();
                    // 跑步初始数据
                    $run->fill($request->all());
                    $run->save();
                    if($request->has('img')){
                        $image = new Images();
                        $img = $request->img;
                        $img['key'] = 'run';
                        $img['key_id'] = $run->id;
                        $image->fill($img);
                        $image->save();
                    }
                DB::commit();
                // 处理返回数据
                $data = RRuns::where('ruid', $run->id)->first();
                return returnData(true, '操作成功', $data);
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少ruid或者rid');
        }
    }

    /**  
     * 分享到动态圈子
     */
    public function doShare(Request $request){
        if($request->has('ruid') && $request->has('rid')){
            $run = RRuns::where('ruid', $request->ruid)->where('rid', $request->rid)->first();
            $shareImg = Images::where('key', 'run')->where('key_id', $request->ruid)->first();
            if($run && $run->isshared==0){
                if($shareImg || $request->has('text')){ //图片和文字，必须有一个
                    try {
                        DB::beginTransaction();
                        //更新运动分享标志
                        DB::table('r_runs')
                            ->where('ruid', $request->ruid)
                            ->update(['isshared' => 1]);
                        //新建动态：type=1 打卡分享类型
                        $moment = new RMoments();
                        $moment->fillable(['rid', 'text', 'type']);
                        $moment->fill([
                            'rid' => $run->rid,
                            'text' => $request->has('text') ? $request->text : "",
                            'type' => 1   //打卡分享1
                        ]);
                        $moment->save();
                        //将运动图复制到动态
                        if($shareImg){
                            $shareImg = $shareImg->toArray();
                            $shareImg['key'] = 'moment';
                            $shareImg['key_id'] = $moment->id;
                            $image = new Images();
                            $image->fill($shareImg);
                            $image->save();
                        }
                        DB::commit();
                        return returnData(true, '操作成功');
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return returnData(false, $th);
                    }
                }else{
                    return returnData(false, '图片和文字，必须有一个');
                }
            }else{
                return returnData(false, '您已经分享过了', '或者ruid和rid不匹配');
            }
        }else{
            return returnData(false, '缺少ruid或者rid');
        }
    }

    /** 
     * 获取周榜
     */
    public function getWeekrank(Request $request){
        if($request->has('team')){
            $timeStart = date('Y-m-d', strtotime("this week"))." 00:00:00";
            $timeEnd = date('Y-m-d', strtotime("+1 week -1 day", strtotime("this week")))." 23:59:59";
            try {
                $top100 = RRuns::join('r_users', 'r_users.rid', '=', 'r_runs.rid')
                                ->where('r_users.team', $request->team)
                                ->where('r_runs.distance', '<>', null) //排除未完成运动
                                ->whereBetween('r_runs.created_at', [$timeStart, $timeEnd])
                                ->select(
                                    DB::raw(
                                        'r_users.rid, 
                                        r_users.nickname, 
                                        r_users.img, 
                                        r_users.team, 
                                        cast(sum(r_runs.distance) as decimal(15,2)) as sumD,
                                        sum(r_runs.time_run) as sumT, 
                                        cast(avg(r_runs.speed) as decimal(15,2)) as avgS'
                                        ))
                                ->groupBy('r_runs.rid')
                                ->orderBy('sumD', 'desc')
                                ->limit(100)
                                ->get();
                return returnData(true, "操作成功", $top100->toArray());
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少team校区');
        }
    }

    /** 
     * 获取月榜
     */
    public function getMonthrank(Request $request){
        if($request->has('team')){
            $timeStart = date("Y-m-01")." 00:00:00";
            $timeEnd = date('Y-m-d', strtotime("$timeStart +1 month -1 day"))." 23:59:59";
            try {
                $top100 = RRuns::join('r_users', 'r_users.rid', '=', 'r_runs.rid')
                                ->where('r_users.team', $request->team)
                                ->where('r_runs.distance', '<>', null) //排除未完成运动
                                ->whereBetween('r_runs.created_at', [$timeStart, $timeEnd])
                                ->select(
                                    DB::raw(
                                        'r_users.rid, 
                                        r_users.nickname, 
                                        r_users.img, 
                                        r_users.team, 
                                        cast(sum(r_runs.distance) as decimal(15,2)) as sumD,
                                        sum(r_runs.time_run) as sumT, 
                                        cast(avg(r_runs.speed) as decimal(15,2)) as avgS'
                                        ))
                                ->groupBy('r_runs.rid')
                                ->orderBy('sumD', 'desc')
                                ->limit(100)
                                ->get();
                return returnData(true, "操作成功", $top100->toArray());
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少team校区');
        }
    }

    /**  
     * 通过id获取某次运动
     */
    public function getRunById(Request $request){
        if($request->has('ruid')){
            try {
                $run = RRuns::where('ruid', $request->ruid)->first();
                //获取图片
                $run['img'] = Images::where('key', 'run')
                                    ->where('key_id', $request->ruid)
                                    ->select('original', 'thumbnail')
                                    ->first();
                return returnData(true, '操作成功', $run);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少ruid');
        }
    }

    /** 
     * 获取个人运动列表
     */
    public function getMyRuns(Request $request){
        if($request->has('rid')){
            $request->has('pageindex') ? $pageindex = $request->pageindex+1 : $pageindex = 1;  //当前页 1,2,3,...,首次查询可以传0
            $request->has('pagesize') ? $pagesize = $request->pagesize : $pagesize = 10;  //页面大小
            try {
                // $runs = RRuns::join('images', function($join){
                //                     $join->on('r_runs.ruid', '=', 'images.key_id')
                //                          ->where('images.key', '=', 'run');
                //                 }) //联合图片会导致只能查询到已完成的运动
                //             ->select('r_runs.*', 'images.original', 'images.thumbnail')
                //             ->where('r_runs.rid', $request->rid)
                //             ->orderBy('created_at', 'desc')
                //             ->skip(($pageindex-1)*$pagesize)
                //             ->take($pagesize)
                //             ->get();
                            
                $runs = RRuns::where('rid', $request->rid)
                            ->orderBy('created_at', 'desc')
                            ->skip(($pageindex-1)*$pagesize)
                            ->take($pagesize)
                            ->get();
                for($n = 0; $n<count($runs); $n++){
                    //获取图片
                    $runs[$n]['imgs'] = Images::where('key', 'run')
                                            ->where('key_id', $runs[$n]['ruid'])
                                            ->select('original', 'thumbnail')
                                            ->first();
                }
                //返回数据处理
                $re = [
                    'rid' => $request->rid,
                    'pageindex' => $pageindex,
                    'pagesize' => $pagesize,
                    'runs' => $runs
                ];
                return returnData(true, "操作成功", $re);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺少rid", null);
        }
    }
}
