<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\RUsers;
use App\LinkUHs;
use App\RHonors;

class RUsersController extends Controller
{
    /**
     * 用户授权注册
     */
    public function regster(Request $request){
        if ($request->has('openid')) {
            $user = new RUsers();
            $linkhonor = new LinkUHs();
            try {
                DB::beginTransaction();
                    // 用户数据
                    $user->fillable(array_keys($request->all()));
                    $user->fill($request->all());
                    $user->save();
                    // 注册即获得初级称号
                    $lv1 = RHonors::first();
                    $linkhonor->fill([
                        'rid' =>  $user->id,
                        'hoid' => $lv1->hoid
                    ]);
                    $linkhonor->save();
                DB::commit();
                return returnData(true, '操作成功', RUsers::where('rid', $user->id)->first());
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少openid');
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
                    return returnData(false, $th);
                }
            }else{
                try {
                    return returnData(true, '操作成功', RUsers::where('rid', $request->rid)->first());
                } catch (\Throwable $th) {
                    return returnData(false, $th);
                }
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }

    /** 
     * 获取用户信息（含勋章称号）
     */
    public function getUserAll(Request $request){
        if ($request->has('rid')) {
            try {
                // 获取用户基本信息
                $data = RUsers::where('rid', $request->rid)->first();
                // 获取称号
                $data['honors'] = LinkUHs::join('r_honors', 'link_u_hs.hoid', '=', 'r_honors.hoid')
                        ->where('rid', $request->rid)
                        ->select('link_u_hs.*', 'r_honors.desc', 'r_honors.name')
                        ->orderBy('created_at', 'desc')
                        ->first();
                // 获取勋章（遗留：需等待勋章业务实现）

                return returnData(true, '操作成功', $data);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少rid', null);
        }
    }

    
    /** 
     * 获取已获称号
     */
    public function getHonor(Request $request){
        if ($request->has('rid')) {
            try {
                // 获取称号
                $data = LinkUHs::join('r_honors', 'link_u_hs.hoid', '=', 'r_honors.hoid')
                        ->where('rid', $request->rid)
                        ->select('link_u_hs.*', 'r_honors.desc', 'r_honors.name')
                        ->orderBy('created_at', 'asc')
                        ->get();
                return returnData(true, '操作成功', $data);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少rid', null);
        }
    }

    /**
     * 修改用户信息
     */
    public function doUpdate(Request $request){
        if ($request->has('openid') || $request->has('rid')) {
            $user = null;
            try {
                // 获取信息
                if ($request->has('openid')) {
                    $user = RUsers::where('openid', $request->openid);
                }else{
                    $user = RUsers::where('rid', $request->rid);
                }
                // 更新
                if($user->first()){
                    if($user->update($request->all())){
                        if ($request->has('openid')) {
                            $data = RUsers::where('openid', $request->openid)->first();
                        }else{
                            $data = RUsers::where('rid', $request->rid)->first();
                        }
                        return returnData(true, '操作成功', $data);
                    }
                    else return returnData(false, '保存失败', null);
                }else{
                    return returnData(false, '不存在该用户', null);
                }
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }

    /**
     * 注销账户
     */
    public function doUnset(Request $request){
        if ($request->has('openid') || $request->has('rid')) {
            try {
                if ($request->has('openid')) {
                    RUsers::where('openid', $request->openid)->delete();
                }else{
                    RUsers::where('rid', $request->rid)->delete();
                }
                return returnData(true, "操作成功", null);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }
}
