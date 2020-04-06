<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\RRuns;
use App\Hitokoto;
use App\Images;
use App\RMoments;

class RunController extends Controller
{
    //获取随机一言
    public function getHitokoto(Request $request){
        if($request->has('type')) $url = 'http://v1.alapi.cn/api/hitokoto?format=json&type='.$request->type;
        else $url = 'http://v1.alapi.cn/api/hitokoto?format=json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
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
                return returnData(true, "操作成功", $json->data);
            }else{
                return returnData(false, $json);
            }
        }else{
            return returnData(false, curl_error($curl));
        }
    }

    //跑步开始
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

    //跑步结束
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

    //分享到动态圈子
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
}
