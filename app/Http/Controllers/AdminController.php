<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RHonors;

class AdminController extends Controller
{
    // 初始数据
    protected $honors = [
        ['name' => '赤脚', 'desc' => 'lv0'],
        ['name' => '草鞋', 'desc' => 'lv1'],
        ['name' => '棉鞋', 'desc' => 'lv2'],
        ['name' => '布鞋', 'desc' => 'lv3'],
        ['name' => '板鞋', 'desc' => 'lv4'],
        ['name' => '高跟鞋', 'desc' => 'lv5'],
        ['name' => '球鞋', 'desc' => 'lv6'],
        ['name' => '运动鞋', 'desc' => 'lv7'],
        ['name' => '跑鞋', 'desc' => 'lv8']
    ];
    // 初始化
    public function initData(Request $request){
        if($request->has('key')){
            if($request->key == "123123"){
                try {
                    // 初始化称号数据
                    foreach($this->honors as $honor){
                        $rHonors = new RHonors();
                        $rHonors->fillable(array_keys($honor));
                        $rHonors->fill($honor);
                        $rHonors->save();
                    }
                    
                    return returnData(true, "初始化完成", null);
                } catch (\Throwable $th) {
                    returnData(false, $th);
                }
            }else{
                return returnData(false, 'key错误，非法操作', null);
            }
        }else{
            return returnData(false, '没有key，非法操作', null);
        }
    }
}
