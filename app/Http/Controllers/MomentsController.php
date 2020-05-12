<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Storage;
use App\RUsers;
use App\RMoments;
use App\Comments;
use App\LinkULikeMs;
use App\RMomentImgs; //废弃，使用images
use App\Images;
use App\Http\Controllers\SystemController as System;

class MomentsController extends Controller
{
    // 发布动态
    public function doMoment(Request $request)
    {
        if ($request->has('rid')) {
            if ($request->has('text') || $request->has('imgs')) {
                $user = RUsers::where('rid', $request->rid);
                if ($user->first()) {
                    $moment = new RMoments();
                    $moment->fill([
                        'rid' => $request->rid,
                        'text' => $request->has('text') ? $request->text : null,
                        'location' => $request->has('location') ? $request->location : null,
                        'latitude' => $request->has('latitude') ? $request->latitude : null,
                        'longitude' => $request->has('longitude') ? $request->longitude : null
                    ]);
                    try {
                        DB::beginTransaction();
                        $moment->save();
                        // 返回数据
                        $data = $moment;
                        if ($request->has('imgs')) {
                            $original = [];
                            $thumbnail = [];
                            $i = 0;
                            foreach ($request->imgs as $img) {
                                $img['key'] = 'moment';
                                $img['key_id'] = $moment->id;

                                $momentImg = new Images();
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
                            $data['imgs'] = [
                                'original' => $original,
                                'thumbnail' => $thumbnail
                            ];
                        }
                        $data['moid'] = $moment->id;
                        unset($data['id']); //修改id为moid，与数据库保持一致
                        DB::commit();
                        return returnData(true, "操作成功", $data);
                    } catch (\Throwable $th) {
                        DB::rollback();
                        return returnData(false, $th);
                    }
                } else {
                    return returnData(false, '不存在该用户', null);
                }
            } else {
                return returnData(false, "需要文字内容或者图片", null);
            }
        } else {
            return returnData(false, "缺rid", null);
        }
    }

    //删除动态
    public function delMoment(Request $request)
    {
        if ($request->has('rid') && $request->has('moid')) {
            DB::beginTransaction(); //事务开始
            try {
                Comments::where('moid', $request->moid)->delete(); //删除评论
                LinkULikeMs::where('moid', $request->moid)->delete(); //删除点赞
                Images::where('key', 'moment')->where('key_id', $request->moid)->delete(); //删除图片
                // Stroage::delete()  //删除图片文件  技术原因暂时不做
                RMoments::where('moid', $request->moid)->where('rid', $request->rid)->delete(); //删除动态
                DB::commit(); //提交事务
                return returnData(true, "操作成功", null);
            } catch (\Throwable $th) {
                DB::rollback(); //回滚
                return returnData(false, $th);
            }
        } else {
            return returnData(false, "缺rid或者moid", null);
        }
    }

    // 发表评论
    public function doComment(Request $request){
        if($request->has('rid') && $request->has('moid') && $request->has('comment')){
            $comment = new Comments();
            $comment->fill([
                'rid' => $request->rid,
                'moid' => $request->moid,
                'comment' => $request->comment
            ]);
            try {
                DB::beginTransaction();
                $comment->save();
                // 返回数据
                $data = $comment;
                $data['coid'] = $comment->id;
                unset($data['id']); //修改id为coid，与数据库保持一致
                DB::commit();
                //生成通知信息
                $moment = RMoments::where('moid', $request->moid)->first();
                $notice = System::systemNotice([
                    'from' => $data->rid,
                    'to' => $moment->rid,
                    'type' => 2,
                    'msg' => $data->comment
                ]);
                return returnData(true, "操作成功", $data);
            } catch (\Throwable $th) {
                DB::rollback();
                return returnData(false, $th);
            }
        } else {
            return returnData(false, "缺rid、moid或者评论内容", null);
        }
    }

    // 删除评论
    public function delComment(Request $request){
        if($request->has('coid')){
            DB::beginTransaction(); //事务开始
            try {
                Comments::where('coid', $request->coid)->delete(); //删除评论
                DB::commit(); //提交事务
                return returnData(true, "操作成功", null);
            } catch (\Throwable $th) {
                DB::rollback(); //回滚
                return returnData(false, $th);
            }
        }else{
            return returnData(false, "缺coid", null);
        }
    }

    // 点赞
    public function doLike(Request $request)
    {
        if ($request->has('rid') && $request->has('moid')) {
            $like = new LinkULikeMs();
            $like->fill([
                'rid' => $request->rid,
                'moid' => $request->moid
            ]);
            try {
                DB::beginTransaction();
                $like->save();
                DB::commit();
                $moment = RMoments::where('moid', $request->moid)->first();
                System::systemNotice([
                    'from' => $request->rid,
                    'to' => $moment->rid,
                    'type' => 1
                ]);
                return returnData(true, "操作成功", $like);
            } catch (\Throwable $th) {
                DB::rollback();
                return returnData(false, $th);
            }
        } else {
            return returnData(false, "缺rid或者moid", null);
        }
    }

    // 取消点赞
    public function doDislike(Request $request)
    {
        if ($request->has('rid') && $request->has('moid')) {
            try {
                LinkULikeMs::where('rid', $request->rid)->where('moid', $request->moid)->delete();
                return returnData(true, "操作成功", null);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        } else {
            return returnData(false, "缺rid或者moid", null);
        }
    }

    // 获取个人动态  手动分页:)
    public function getMine(Request $request)
    {
        if ($request->has('rid')) {
            $request->has('pageindex') ? $pageindex = $request->pageindex + 1 : $pageindex = 1;  //当前页 1,2,3,...,首次查询可以传0
            $request->has('pagesize') ? $pagesize = $request->pagesize : $pagesize = 10;  //页面大小
            $pageall = null; //总页数
            try {
                $moments = RMoments::join('r_users', 'r_users.rid', '=', 'r_moments.rid')
                    ->select('r_moments.*', 'r_users.nickname', 'r_users.img')
                    ->where('r_moments.rid', $request->rid)
                    ->orderBy('created_at', 'desc')
                    // ->skip(($pageindex-1)*$pagesize)
                    // ->take($pagesize)
                    ->get();
                $moment_num = count($moments); //动态总条数
                $pageall = ceil($moment_num / $pagesize); //计算总页数
                $data = []; //动态联合数据
                for ($i = ($pageindex - 1) * $pagesize, $n = 0; $i < ($pageindex - 1) * $pagesize + $pagesize && $i < $moment_num; $i++, $n++) {
                    $data[$n] = $moments[$i];
                    //获取评论
                    $data[$n]['comments'] = Comments::join('r_users', 'r_users.rid', '=', 'comments.rid')
                        ->where('moid', $moments[$i]['moid'])
                        ->select('comments.*', 'r_users.nickname', 'r_users.img')
                        ->orderBy('created_at', 'asc')
                        ->get();
                    //获取点赞
                    $data[$n]['likes'] = LinkULikeMs::join('r_users', 'r_users.rid', '=', 'link_u_like_ms.rid')
                        ->where('moid', $moments[$i]['moid'])
                        ->select('link_u_like_ms.*', 'r_users.img')
                        ->orderBy('created_at', 'asc')
                        ->get();
                    //获取图片
                    $imgs = Images::where('key', 'moment')->where('key_id', $moments[$i]['moid'])->get();
                    $original = [];
                    $thumbnail = [];
                    foreach ($imgs as $img) {
                        $original[] = [
                            "url" => $img->original,
                            "width" => $img->width,
                            "height" => $img->height
                        ];
                        $thumbnail[] = [
                            "url" => $img->thumbnail,
                            "width" => $img->mwidth,
                            "height" => $img->mheight
                        ];
                    }
                    $data[$n]['imgs'] = [
                        'original' => $original,
                        'thumbnail' => $thumbnail
                    ];
                }
                //返回数据处理
                $re = [
                    'rid' => $request->rid,
                    'pageindex' => $pageindex,
                    'pagesize' => $pagesize,
                    'pageall' => $pageall,
                    'moments' => $data
                ];
                return returnData(true, "操作成功", $re);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        } else {
            return returnData(false, "缺少rid", null);
        }
    }

    // 获取动态
    public function getMoments(Request $request)
    {
        $request->has('pageindex') ? $pageindex = $request->pageindex + 1 : $pageindex = 1;  //当前页 1,2,3,...,首次查询可以传0
        $request->has('pagesize') ? $pagesize = $request->pagesize : $pagesize = 10;  //页面大小
        try {
            $moments = RMoments::join('r_users', 'r_users.rid', '=', 'r_moments.rid')
                ->select('r_moments.*', 'r_users.nickname', 'r_users.img')
                ->orderBy('created_at', 'desc')
                ->skip(($pageindex - 1) * $pagesize)
                ->take($pagesize)
                ->get();
            $data = []; //动态联合数据
            for ($n = 0; $n < count($moments); $n++) {
                $data[$n] = $moments[$n];
                //获取评论
                $data[$n]['comments'] = Comments::join('r_users', 'r_users.rid', '=', 'comments.rid')
                    ->where('moid', $moments[$n]['moid'])
                    ->select('comments.*', 'r_users.nickname')
                    ->orderBy('created_at', 'asc')
                    ->get();
                //获取点赞
                $data[$n]['likes'] = LinkULikeMs::join('r_users', 'r_users.rid', '=', 'link_u_like_ms.rid')
                    ->where('moid', $moments[$n]['moid'])
                    ->select('link_u_like_ms.*', 'r_users.img')
                    ->orderBy('created_at', 'asc')
                    ->get();
                //获取图片
                $imgs = Images::where('key', 'moment')->where('key_id', $moments[$n]['moid'])->get();
                $original = [];
                $thumbnail = [];
                foreach ($imgs as $img) {
                    $original[] = [
                        "url" => $img->original,
                        "width" => $img->width,
                        "height" => $img->height
                    ];
                    $thumbnail[] = [
                        "url" => $img->thumbnail,
                        "width" => $img->mwidth,
                        "height" => $img->mheight
                    ];
                }
                $data[$n]['imgs'] = [
                    'original' => $original,
                    'thumbnail' => $thumbnail
                ];
            }
            //返回数据处理
            $re = [
                'pageindex' => $pageindex,
                'pagesize' => $pagesize,
                'moments' => $data
            ];
            return returnData(true, "操作成功", $re);
        } catch (\Throwable $th) {
            return returnData(false, $th);
        }
    }

    // 获取某条动态
    public function getMomentById(Request $request)
    {
        if ($request->has('moid')) {
            try {
                $moment = RMoments::join('r_users', 'r_users.rid', '=', 'r_moments.rid')
                    ->select('r_moments.*', 'r_users.nickname', 'r_users.img')
                    ->where('moid', $request->moid)
                    ->first();
                $data = []; //动态联合数据
                $data = $moment;
                //获取评论
                $data['comments'] = Comments::join('r_users', 'r_users.rid', '=', 'comments.rid')
                    ->where('moid', $request->moid)
                    ->select('comments.*', 'r_users.nickname')
                    ->orderBy('created_at', 'asc')
                    ->get();
                //获取点赞
                $data['likes'] = LinkULikeMs::join('r_users', 'r_users.rid', '=', 'link_u_like_ms.rid')
                    ->where('moid', $request->moid)
                    ->select('link_u_like_ms.*', 'r_users.img')
                    ->orderBy('created_at', 'asc')
                    ->get();
                //获取图片
                $imgs = Images::where('key', 'moment')->where('key_id', $request->moid)->get();
                $original = [];
                $thumbnail = [];
                foreach ($imgs as $img) {
                    $original[] = [
                        "url" => $img->original,
                        "width" => $img->width,
                        "height" => $img->height
                    ];
                    $thumbnail[] = [
                        "url" => $img->thumbnail,
                        "width" => $img->mwidth,
                        "height" => $img->mheight
                    ];
                }
                $data['imgs'] = [
                    'original' => $original,
                    'thumbnail' => $thumbnail
                ];
                return returnData(true, "操作成功", $data);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
        } else {
            return returnData(false, "缺少moid", null);
        }
    }
    // 获取热门动态
    public function getHot(Request $request)
    {
        // date_default_timezone_set('prc');
        $timeStart = date('Y-m-d  H:i:s', strtotime("-1 week"));
        $timeEnd = date('Y-m-d  H:i:s');
            //order by写成原生的加在那句后面
            // $likes=DB::select('select moid,count(moid) as count from link_u_like_ms where created_at between "'.$timeStart.'" and "'.$timeEnd.'" group by moid order by count desc');
            // $like = $likes[0];//取第一条 同下面的first*()方法一样的效果

            //把前面的查询给拆成一个个方法  first()直接获取最多那一条数据           
            $like=LinkULikeMs::select(DB::raw('moid, count(*) as count'))   
                                ->whereBetween('created_at', [$timeStart, $timeEnd])
                                ->groupBy('moid')
                                ->orderBy('count', 'desc')  //这里将统计的数量排序，下面first取第一条
                                ->first();
          if($likes){
            $hotMoid=$like['moid'];
            try {
                $moment = RMoments::join('r_users', 'r_users.rid', '=', 'r_moments.rid')
                    ->select('r_moments.*', 'r_users.nickname', 'r_users.img')
                    ->where('moid', $hotMoid)
                    ->first();
                $data = []; //动态联合数据
                $data = $moment;
                //获取评论
                $data['comments'] = Comments::join('r_users', 'r_users.rid', '=', 'comments.rid')
                    ->where('moid', $hotMoid)
                    ->select('comments.*', 'r_users.nickname')
                    ->orderBy('created_at', 'asc')
                    ->get();
                //获取点赞
                $data['likes'] = LinkULikeMs::join('r_users', 'r_users.rid', '=', 'link_u_like_ms.rid')
                    ->where('moid', $hotMoid)
                    ->select('link_u_like_ms.*', 'r_users.img')
                    ->orderBy('created_at', 'asc')
                    ->get();
                //获取图片
                $imgs = Images::where('key', 'moment')->where('key_id', $hotMoid)->get();
                $original = [];
                $thumbnail = [];
                foreach ($imgs as $img) {
                    $original[] = [
                        "url" => $img->original,
                        "width" => $img->width,
                        "height" => $img->height
                    ];
                    $thumbnail[] = [
                        "url" => $img->thumbnail,
                        "width" => $img->mwidth,
                        "height" => $img->mheight
                    ];
                }
                $data['imgs'] = [
                    'original' => $original,
                    'thumbnail' => $thumbnail
                ];
                return returnData(true, "操作成功", $data);
            } catch (\Throwable $th) {
                return returnData(false, $th);
            }
          }else{
            return returnData(false, "最近没有点赞数据", null);
          }
           
    }
}
