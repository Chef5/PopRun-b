<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RNotices;
use DB;
use App\RUsers;

class SystemController extends Controller
{
    /** 
     * 生成系统通知
     * 1.$obj['from']：默认系统发出
     * 2.$obj['to']：需要指定！
     * 3.$obj['type']：1点赞，2评论，0系统通知
     * 4.$obj['msg']：系统通知、评论 需要指定内容。
     */
    public static function systemNotice($obj){
        $from = isset($obj['from']) ? $obj['from'] : 0; //没有指定发出者即认为是系统发出
        $to = $obj['to'];
        $type = isset($obj['type']) ? $obj['type'] : 0; //类型:1点赞，2评论，0系统通知
        $msg = isset($obj['msg']) ? $obj['msg'] : '';

        //处理消息内容
        // if($type == 1) $msg = "我觉得您动态很棒！";
        if($from != 0){
            $user = RUsers::where('rid', $from)->first();
            if($type == 1) $msg = '收到来自['.$user->nickname.']的点赞！';
            if($type == 2) $msg = '['.$user->nickname.']评论：'.$msg;
        }else{
            $msg = "系统：".$msg;
        }

        //开始保存
        $notice = new RNotices();
        $notice->from = $from;
        $notice->to = $to;
        $notice->type = $type;
        $notice->msg = $msg;
        try {
            DB::beginTransaction();
                $notice->save();
            DB::commit();
            return $notice;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    /** 
     * 获取系统通知
     */
    public function getNotice(Request $request){
        if($request->has('rid')){
            if($request->has('type')){
                if($request->has('read')){
                    try {
                        $data = RNotices::where('to', $request->rid)
                                        ->where('type', $request->type)
                                        ->where('read', $request->read)
                                        ->get();
                        return returnData(true, '操作成功', $data->toArray());
                    } catch (\Throwable $th) {
                        return returnData(false, $th);
                    }
                }else{
                    try {
                        $data = RNotices::where('to', $request->rid)
                                        ->where('type', $request->type)
                                        ->get();
                        return returnData(true, '操作成功', $data->toArray());
                    } catch (\Throwable $th) {
                        return returnData(false, $th);
                    }
                }
            }else{
                if($request->has('read')){
                    try {
                        $data = RNotices::where('to', $request->rid)
                                        ->where('read', $request->read)
                                        ->get();
                        return returnData(true, '操作成功', $data->toArray());
                    } catch (\Throwable $th) {
                        return returnData(false, $th);
                    }
                }else{
                    try {
                        $data = RNotices::where('to', $request->rid)
                                        ->get();
                        return returnData(true, '操作成功', $data->toArray());
                    } catch (\Throwable $th) {
                        return returnData(false, $th);
                    }
                }
            }
        }else{
            return returnData(false, '缺少rid');
        }
    }

    /**  
     * 阅读通知
     */
    public function readNotice(Request $request){
        if($request->has('noids')){
            $noids = $request->noids;
            try {
                DB::beginTransaction();
                    RNotices::whereIn('noid', $noids)
                            ->update(['read' => 1]);
                    $data = RNotices::whereIn('noid', $noids)->get();
                DB::commit();
                return returnData(true, '操作成功', $data->toArray());
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少noid');
        }
    }
    
    /**  
     * 删除通知
     */
    public function delNotice(Request $request){
        if($request->has('noids')){
            $noids = $request->noids;
            try {
                DB::beginTransaction();
                    RNotices::whereIn('noid', $noids)
                            ->delete();
                DB::commit();
                return returnData(true, '操作成功');
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少noid');
        }
    }
}
