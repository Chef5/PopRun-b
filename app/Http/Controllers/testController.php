<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\RRuns;
use App\RUsers;
use App\RMedals;
use App\LinkUMs;
use App\Http\Controllers\SystemController as System;

class testController extends Controller
{
    //月勋章授予(累计运动7次)
    public function grantMonthMedal(Request $request){
        $medalKey = date('Y_m',strtotime('-1 month'));
        $timeStart = date('Y-m-01 00:00:00',strtotime('-1 month'));
        $timeEnd = date('Y-m-d', strtotime("$timeStart +1 month -1 day"))." 23:59:59";
        //统计上月每个用户的运动次数
        $userRuns = RUsers::join('r_runs', 'r_users.rid', '=', 'r_runs.rid')
                            ->where('r_runs.distance', '<>', null) //排除未完成运动
                            ->whereBetween('r_runs.created_at', [$timeStart, $timeEnd])
                            ->select(
                                DB::raw(
                                    'r_users.rid,
                                    count(r_runs.ruid) as count
                                    '
                                )
                            )
                            ->groupBy('r_runs.rid')
                            ->get();
        $medal = RMedals::where('mkey', $medalKey)->first();
        foreach($userRuns as $user){
            if($user->count >= 7){
                $me = new LinkUMs();
                $me->fill([
                    'rid' => $user->rid,
                    'meid' => $medal->meid
                ]);
                $me->save();
            }
        }
        // return returnData(true, [$timeStart,$timeEnd]);
    }

    //季勋章授予(三个月累计运动45次)
    public function grantSeasonMedal(Request $request){
        $seasons = ['a', 'b', 'c', 'd'];
        $medalKey = date('Y',strtotime('-4 month')).'_'.$seasons[date('m',strtotime('-4 month'))/3];
        $timeStart = date('Y-m-01 00:00:00',strtotime('-4 month'));
        $timeEnd = date('Y-m-d', strtotime("$timeStart +3 month -1 day"))." 23:59:59";
        //统计前三个月每个用户的运动次数
        $userRuns = RUsers::join('r_runs', 'r_users.rid', '=', 'r_runs.rid')
                            ->where('r_runs.distance', '<>', null) //排除未完成运动
                            ->whereBetween('r_runs.created_at', [$timeStart, $timeEnd])
                            ->select(
                                DB::raw(
                                    'r_users.rid,
                                    count(r_runs.ruid) as count
                                    '
                                )
                            )
                            ->groupBy('r_runs.rid')
                            ->get();
        $medal = RMedals::where('mkey', $medalKey)->first();
        foreach($userRuns as $user){
            if($user->count >= 45){
                $me = new LinkUMs();
                $me->fill([
                    'rid' => $user->rid,
                    'meid' => $medal->meid
                ]);
                if($me->save()){
                    System::systemNotice([
                        'from' => 0, 
                        'to' => $user->rid, 
                        'type' => 0, 
                        'msg' => "你新获得一枚勋章<".$medal->name.">"
                    ]);
                }
            }
        }
        // return returnData(true, [$timeStart,$timeEnd]);
    }

    
    //获取月排行榜勋章：校区前100 rank_a，校区前10，校前1，国前100
    public function grantRankingMedal(Request $request){
        $ranks = ['rank_a', 'rank_b', 'rank_c', 'rank_d'];

        $timeStart = date('Y-m-01 00:00:00',strtotime('-1 month'));
        $timeEnd = date('Y-m-d', strtotime("$timeStart +1 month -1 day"))." 23:59:59";

        $schools = RUsers::where('team', '<>', 'system')->select('team')->distinct()->get();
        
        $rank_a = RMedals::where('mkey', $ranks[0])->first(); //校区前100
        $rank_b = RMedals::where('mkey', $ranks[1])->first(); //校区前10
        $rank_c = RMedals::where('mkey', $ranks[2])->first(); //校前1
        $rank_d = RMedals::where('mkey', $ranks[3])->first(); //国前100

        foreach($schools as $school){
            //校区前100
            $top100school = RRuns::join('r_users', 'r_users.rid', '=', 'r_runs.rid')
                            ->where('r_users.team', $school->team)
                            ->where('r_runs.distance', '<>', null) //排除未完成运动
                            ->whereBetween('r_runs.created_at', [$timeStart, $timeEnd])
                            ->select(
                                DB::raw(
                                    'r_users.rid, 
                                    cast(sum(r_runs.distance) as decimal(15,2)) as sumD'
                                    ))
                            ->groupBy('r_runs.rid')
                            ->orderBy('sumD', 'desc')
                            ->limit(100)
                            ->get();
            for( $i=0; $i<count($top100school); $i++ ){
                if( $i == 0 ){ //校前1
                    $me = new LinkUMs();
                    $me->fill([
                        'rid' => $top100school[$i]->rid,
                        'meid' => $rank_c->meid
                    ]);
                    $me->save();
                    $this->noticeUser($top100school[$i]->rid, $rank_c->name);
                }else
                if( $i <= 10 ){ //校区前10
                    $me = new LinkUMs();
                    $me->fill([
                        'rid' => $top100school[$i]->rid,
                        'meid' => $rank_b->meid
                    ]);
                    $me->save();
                    $this->noticeUser($top100school[$i]->rid, $rank_b->name);
                }else{          //校区前100
                    $me = new LinkUMs();
                    $me->fill([
                        'rid' => $top100school[$i]->rid,
                        'meid' => $rank_a->meid
                    ]);
                    $me->save();
                    $this->noticeUser($top100school[$i]->rid, $rank_a->name);
                }
            }
        }   

        //全国前100
        $top100 = RRuns::join('r_users', 'r_users.rid', '=', 'r_runs.rid')
                        ->where('r_runs.distance', '<>', null) //排除未完成运动
                        ->whereBetween('r_runs.created_at', [$timeStart, $timeEnd])
                        ->select(
                            DB::raw(
                                'r_users.rid, 
                                cast(sum(r_runs.distance) as decimal(15,2)) as sumD'
                                ))
                        ->groupBy('r_runs.rid')
                        ->orderBy('sumD', 'desc')
                        ->limit(100)
                        ->get();
        foreach($top100 as $user){
            $me = new LinkUMs();
            $me->fill([
                'rid' => $user->rid,
                'meid' => $rank_d->meid
            ]);
            $me->save();
            $this->noticeUser($user->rid, $rank_d->name);
        }
        // return returnData(true, [$timeStart,$timeEnd]);
    }

    private function noticeUser($rid, $medalName){
        System::systemNotice([
            'from' => 0, 
            'to' => $rid, 
            'type' => 0, 
            'msg' => "你新获得一枚勋章<".$medalName.">"
        ]);
    }
}
