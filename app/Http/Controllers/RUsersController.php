<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RUsers;

class RUsersController extends Controller
{
    /**
     * 用户授权注册
     */
    public function regster(Request $request){
        if ($request->has('openid')) {
            $user = new RUsers();
            try {
                $user->fillable(array_keys($request->all()));
                $user->fill($request->all());
                $user->save();
                return returnData(true, '操作成功', RUsers::where('rid', $user->id)->first());
            } catch (\Throwable $th) {
                return returnData(false, $th->errorInfo[2], null);
            }
        }else{
            return returnData(false, '缺少openid', null);
        }
    }
    
    /**
     * 获取用户信息
     */
    public function getUser(Request $request){
        if ($request->has('openid') || $request->has('rid')) {
            if ($request->has('openid')) {
                try {
                    return returnData(true, '操作成功', RUsers::where('openid', $request->openid)->first());
                } catch (\Throwable $th) {
                    return returnData(false, $th->errorInfo[2], null);
                }
            }else{
                try {
                    return returnData(true, '操作成功', RUsers::where('rid', $request->rid)->first());
                } catch (\Throwable $th) {
                    return returnData(false, $th->errorInfo[2], null);
                }
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }
}
