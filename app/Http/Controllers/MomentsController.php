<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RUsers;
use App\RMoments;
use App\Comments;
use App\LinkULikeMs;
use App\RMomentImgs;

class MomentsController extends Controller
{
    // 发布动态
    public function doMoment(Request $request){
        if($request->has('rid')){
            if($request->has('text') || $request->has('imgs')){
                $user = RUsers::where('rid', $request->rid);
                if($user->first()){
                    $moment = new RMoments();
                    $moment->fillable(['rid', 'text']);
                    $moment->fill([
                        'rid' => $request->rid,
                        'text' => $request->has('text') ? $request->text : ""
                    ]);
                    try {
                        if($moment->save()){
                            $original = []; $thumbnail = []; $i = 0;
                            foreach($request->imgs as $img){
                                $img['moid'] = $moment->id;
                                $momentImg = new RMomentImgs();
                                $momentImg->fillable(array_keys($img));
                                $momentImg->fill($img);
                                $momentImg->save();

                                $original[$i]['url'] = $img['original'];
                                $original[$i]['width'] = $img['width'];
                                $original[$i]['height'] = $img['height'];
                                $thumbnail[$i]['url'] = $img['thumbnail'];
                                $thumbnail[$i]['width'] = $img['mwidth'];
                                $thumbnail[$i]['height'] = $img['mheight'];
                                $i++;
                            }
                            // 返回数据
                            $data = $moment;
                            $data['moid'] = $moment->id; unset($data['id']); //修改id为moid，与数据库保持一致
                            $data['imgs'] = [
                                'original' => $original,
                                'thumbnail' => $thumbnail
                            ];
                            return returnData(true, "操作成功", $data);
                        }
                    } catch (\Throwable $th) {
                        return returnData(false, $th->errorInfo[2], null);
                    }
                }else{
                    return returnData(false, '不存在该用户', null);
                }
            }else{
                return returnData(false, "需要文字内容或者图片", null);
            }
        }else{
            return returnData(false, "缺rid", null);
        }
    }

    // 发表评论
    public function doComment(Request $request){
        if($request->has('rid') && $request->has('moid')){
            $comment = new Comments();
            $comment->fill([
                'rid' => $request->rid,
                'moid' => $request->moid,
                'comment' => $request->comment
            ]);
            try {
                if($comment->save()){
                    // 返回数据
                    $data = $comment;
                    $data['coid'] = $comment->id; unset($data['id']); //修改id为coid，与数据库保持一致
                    return returnData(true, "操作成功", $data);
                }
            } catch (\Throwable $th) {
                return returnData(false, $th->errorInfo[2], null);
            }
        }else{
            return returnData(false, "缺rid或者moid", null);
        }
    }
}
