<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\RRuns;
use App\Hitokoto;
use App\Images;
use App\RMoments;
use App\RUsers;
use App\RMedals;
use App\LinkUMs;
use App\LinkUAs;
use App\Http\Controllers\SystemController as System;

class RunController extends Controller
{
    /**
     * 单次里程成就：5km, 10km, 15km, 20km, 半马(21.0975=21.09), 全马(42.195=42.19), 50km, 100km
     * 活动成就：r_activitys.distance
     */
    private function checkMedals($rid, $distance){
        // 单次里程成就
        $distances = [5, 10, 15, 20, 21.09, 42.19, 50, 100];
        $medalkeys = ['star_1_act','star_2_act','star_3_act','star_4_act','star_5_act','star_6_act','star_7_act','star_8_act'];
        if($distance>=5){
            $index = 0;  //
            for($i=0; $i<count($distances); $i++){
                if($distance>=$distances[$i]) $index = $i;
                else break;
            }
            $medal = RMedals::where('mkey', $medalkeys[$index])->first();
            $isAchieved = LinkUMs::where('rid', $rid)->where('meid', $medal->meid)->get();
            if(count($isAchieved)==0){  //未获取过，可以进行授予
                $me = new LinkUMs();
                $me->fill([
                    'rid' => $rid,
                    'meid' => $medal->meid
                ]);
                $me->save();
                System::systemNotice([
                    'from' => 0,
                    'to' => $rid,
                    'type' => 0,
                    'msg' => "你新获得一枚勋章<".$medal->name.">"
                ]);
            }
        }
        // 活动成就
        $timeNow = date('Y-m-d H:i:s');
        $theLastestFinished = LinkUAs::join('r_activitys', 'link_u_as.acid', '=', 'r_activitys.acid')
                ->select('link_u_as.rid', 'link_u_as.acid', 'link_u_as.isfinished', 'r_activitys.title', 'r_activitys.distance', 'r_activitys.period', 'r_activitys.meid')
                ->where('r_activitys.period', '>', $timeNow)  //有效期内的活动
                ->where('link_u_as.rid', $rid)  //已报名的活动
                ->where('link_u_as.isfinished', 0) //未完成的活动
                ->where('r_activitys.distance', '<=', $distance)  //达到挑战条件
                ->orderBy('r_activitys.period', 'asc')  //升序
                ->first();  //获取最近将要过期的
        if($theLastestFinished){
            $medal = RMedals::where('meid', $theLastestFinished->meid)->first();
            try {
                DB::beginTransaction();
                    // 更新记录为已完成
                    LinkUAs::where('rid', $theLastestFinished->rid)
                            ->where('acid', $theLastestFinished->acid)
                            ->update(['isfinished'=>1]);
                    // 勋章授予
                    $me = new LinkUMs();
                    $me->fill([
                        'rid' => $theLastestFinished->rid,
                        'meid' => $theLastestFinished->meid
                    ]);
                    $me->save();
                DB::commit();
                System::systemNotice([
                    'to' => $theLastestFinished->rid,
                    'msg' => "恭喜您挑战完成活动《".$theLastestFinished->title."》，获得一枚勋章<".$medal->name.">"
                ]);
            } catch (\Throwable $th) {
                DB::rollback();
            }
        }

    }
    /**
     * 获取随机一言
     */
    public function getHitokoto(Request $request){
        // TODO: 这个地址是我转发的，我不确定什么时候可能会关服，如需要稳当，请使用随机一言接口官方：http://alapi.cn/api/view/1
        $url = 'https://mood.1zdz.cn/api/hig/getHitokoto';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = [];
        for($i=0; $i<10; $i++){
            $returnjson=curl_exec($curl);
            if($returnjson){
                //整理返回数据
                $json = json_decode($returnjson);
                if($json->isSuccess){
                    $hitokoto = new Hitokoto();
                    $hitokoto['hitokoto'] = $json->data->text;
                    $hitokoto['type'] = $json->data->type;
                    $hitokoto['from'] = $json->data->from;
                    $hitokoto['creator'] = $json->data->creator;
                    $hitokoto->save();
                    array_push($data, $hitokoto);
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
                    DB::commit();
                    $data = RRuns::where('ruid', $request->ruid)->first();
                    // 检测：单次里程勋章
                    $this->checkMedals($data->rid, $request->distance);

                    return returnData(true, '操作成功', $data);
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
                DB::commit();
                // 处理返回数据
                $data = RRuns::where('ruid', $run->id)->first();
                // 检测：单次里程勋章
                $this->checkMedals($request->rid, $request->distance);

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
        if($request->has('ruid') && $request->has('rid') && $request->has('img')){
            $run = RRuns::where('ruid', $request->ruid)->where('rid', $request->rid)->first();
            if($run && $run->isshared==0){
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
                            'text' => $request->has('text') ? $request->text : null,
                            'type' => 1   //打卡分享1
                        ]);
                        $moment->save();
                        //保存分享图
                        $image = new Images();
                        $img = $request->img;
                        $img['key'] = 'moment';
                        $img['key_id'] = $moment->id;
                        $image->fill($img);
                        $image->save();
                    DB::commit();
                    return returnData(true, '操作成功');
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return returnData(false, $th);
                }
            }else{
                return returnData(false, '您已经分享过了', '或者ruid和rid不匹配');
            }
        }else{
            return returnData(false, '缺少ruid或者rid，或者分享图');
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
     * 获取排行榜：周榜，月榜合并接口 type:0周榜 1月榜
     */
    public function getRanking(Request $request){
        if($request->has('team')){
            //默认月榜
            $timeStart = date("Y-m-01")." 00:00:00";
            $timeEnd = date('Y-m-d', strtotime("$timeStart +1 month -1 day"))." 23:59:59";
            if($request->has('type') && $request->type == 0){  //0周榜
                $timeStart = date('Y-m-d', strtotime("this week"))." 00:00:00";
                $timeEnd = date('Y-m-d', strtotime("+1 week -1 day", strtotime("this week")))." 23:59:59";
            }
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
     * 获取个人排行榜信息：周榜，月榜合并接口 type:0周榜 1月榜
     */
    public function getMyRanking(Request $request){
        if($request->has('rid')){
            //默认月榜
            $timeStart = date("Y-m-01")." 00:00:00";
            $timeEnd = date('Y-m-d', strtotime("$timeStart +1 month -1 day"))." 23:59:59";
            if($request->has('type') && $request->type == 0){  //0周榜
                $timeStart = date('Y-m-d', strtotime("this week"))." 00:00:00";
                $timeEnd = date('Y-m-d', strtotime("+1 week -1 day", strtotime("this week")))." 23:59:59";
            }
            try {
                $user = RUsers::where('rid', $request->rid)->first();
                $result = RRuns::join('r_users', 'r_users.rid', '=', 'r_runs.rid')
                                ->where('r_users.team', $user->team)
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
                                        // rank() over(order by sumD) rank'
                                        ))
                                ->groupBy('r_runs.rid')
                                ->orderBy('sumD', 'desc')
                                ->get();
                $re = null;
                for($n=0; $n<count($result); $n++){
                    if($result[$n]['rid'] == $user['rid']){
                        $re = $result[$n];
                        $re['rank'] = $n+1;
                        break;
                    }
                }
                return returnData(true, "操作成功", $re);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少rid');
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
                // $run['img'] = Images::where('key', 'run')
                //                     ->where('key_id', $request->ruid)
                //                     ->select('original', 'thumbnail')
                //                     ->first();
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
                // for($n = 0; $n<count($runs); $n++){
                //     //获取图片
                //     $runs[$n]['imgs'] = Images::where('key', 'run')
                //                             ->where('key_id', $runs[$n]['ruid'])
                //                             ->select('original', 'thumbnail')
                //                             ->first();
                // }
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

    /**
     * 获取个人运动数据统计
     */
    public function getMyRunsData(Request $request){
        if($request->has('rid')){
            try {
                $data = RRuns::where('rid', $request->rid)
                            ->where('distance', '<>', null) //排除未完成运动
                            ->select(
                                DB::raw('
                                    count(*) as times,
                                    cast(sum(distance) as decimal(15,2)) as sumD,
                                    cast(max(distance) as decimal(15,2)) as maxD,
                                    sum(time_run) as sumT,
                                    max(time_run) as maxT,
                                    cast(avg(speed) as decimal(15,2)) as avgS,
                                    max(speed) as maxS,
                                    min(speed) as minS
                                '))
                            ->get();
                return returnData(true, "操作成功", $data[0]);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺少rid", null);
        }
    }

    /**
     * 删除记录
     */
    public function delRun(Request $request){
        if($request->has('rid') && $request->has('ruid')){
            try {
                DB::beginTransaction(); //事务开始
                    RRuns::where([
                        ['ruid', '=', $request->ruid],
                        ['rid', '=', $request->rid]
                    ])->delete();
                DB::commit(); //提交事务
                return returnData(true, "操作成功", null);
            } catch (\Throwable $th) {
                DB::rollback(); //回滚
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺rid或者ruid", null);
        }
    }
}
