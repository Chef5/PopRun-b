<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Validator;
use App\RHonors;
use App\RMedals;

class PublicController extends Controller
{
    // 图片上传
    public function uploadImg(Request $request){
        $inputData = $request->all();
        $rules = [
            'img' => [ 'file','image','max:10240' ]
        ];
        $validator = Validator::make($inputData,$rules);
        if($validator->fails()){
            return returnData(false, "校验失败", $validator);
        }
        $photo = $inputData['img'];
        $file_name = uniqid();
        $file_relative_path = 'resources/images/'.date('Y-m-d');
        $file_path = public_path($file_relative_path);
        try {
            if (!is_dir($file_path)){
                mkdir($file_path);
            }
            // 保存缩略图 resources/images/2020-01-31/5e3319bcafcae-min-200x356.jpg
            $dfile = Image::make($photo);
            $min_file_path = '/'.$file_name.'-min-'.'200x'.round(200*$dfile->height()/$dfile->width()).'.'.$photo->getClientOriginalExtension();
            $image = Image::make($photo)->resize(200, null, function ($constraint) {$constraint->aspectRatio();})->save($file_path.$min_file_path);
            // 保存原图   resources/images/2020-01-31/5e3319bcafcae-1080x1920.jpg
            // $original_file_path = '/'.$file_name.'-'.$dfile->width().'x'.$dfile->height().'.'.$photo->getClientOriginalExtension();
            // $image = Image::make($photo)->save($file_path.$original_file_path);  //不能原图保存，服务器带宽获取太慢
            $original_file_path = '/'.$file_name.'-'.'1080x'.round(1080*$dfile->height()/$dfile->width()).'.'.$photo->getClientOriginalExtension();
            $image = Image::make($photo)->resize(1080, null, function ($constraint) {$constraint->aspectRatio();})->save($file_path.$original_file_path);
            // TODO: 处理返回网络url, 本地调试默认用的http://，体验版或者上线需要替换成https://
            $imgUrl = 'http://'.$request->server('HTTP_HOST').'/'.$file_relative_path;
            $data = [
                'name' => $photo->getClientOriginalName(),
                'store' => $file_name,
                'extension' => $photo->getClientOriginalExtension(),
                'mimetype' => $photo->getClientMimeType(),
                'size' => $photo->getSize(),
                'width' => $dfile->width(),
                'height' => $dfile->height(),
                'mwidth' => 200,
                'mheight' => round(200*$dfile->height()/$dfile->width()),
                'original' => $imgUrl.$original_file_path,
                'thumbnail' => $imgUrl.$min_file_path,
                'error' => $photo->getError()
            ];
            return returnData(true, '上传成功', $data);
        } catch (\Throwable $th) {
            return returnData(false, $th->getMessage());
        }
    }
    // 获取称号列表
    public function getHonorAll(){
        $honors = RHonors::get();
        $data = [];
        foreach($honors as $honor){
            array_push($data, ['name'=>$honor->name, 'desc'=>$honor->desc]);
        }
        return returnData(true, "操作成功", $data);
    }
    // 获取openid
    public function getOpenid(Request $request){
        $appid = $request->has('appid') ? $request->appid : env('WX_APPID');
        $secret = $request->has('secret') ? $request->secret : env('WX_SECRET');
        if($request->has('code')){
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$request->code.'&grant_type=authorization_code';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//不验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//不验证主机
            $returnjson=curl_exec($curl);
            if($returnjson){
                //整理返回数据
                $json = json_decode($returnjson);
                if(!property_exists($json, 'errmsg')){
                    return returnData(true, "操作成功", $json);
                }else{
                    return returnData(false, $json->errmsg, null);
                }
            }else{
                return returnData(false, curl_error($curl), null);
            }
        }else{
            return returnData(false, "缺少code", null);
        }
    }
    // 获取勋章列表
    public function getMedalAll(Request $request){
        return returnData(true, "操作成功", RMedals::get());
    }
}
