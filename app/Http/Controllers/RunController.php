<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RRuns;
use App\Hitokoto;

class RunController extends Controller
{
    //获取随机一言
    public function getHitokoto(Request $request){
        if($request->has('type')) $url = 'http://v1.alapi.cn/api/hitokoto?format=json&type='.$request->type;
        else $url = 'http://v1.alapi.cn/api/hitokoto?format=json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//不验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//不验证主机
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
}
