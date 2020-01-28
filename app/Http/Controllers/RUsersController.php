<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RUsers;

class RUsersController extends Controller
{
    //用户授权注册
    public function index(Request $request){
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
}
