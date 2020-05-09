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
        return returnData(true, $userRuns);
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
        return returnData(true, $medal);
    }
}
