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
            if($type == 1) $msg = '收到来自 ['.$user->nickname.'] 的点赞！';
            if($type == 2) $msg = '['.$user->nickname.'] 评论：'.$msg;
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
}
