<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\RRuns;
use App\RUsers;
use App\LinkUHs;
use App\Http\Controllers\SystemController as System;

class GrantHonor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GrantHonor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '称号授予';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //统计每个用户的运动次数
        $userRuns = RUsers::join('r_runs', 'r_users.rid', '=', 'r_runs.rid')
                            ->where('r_runs.distance', '<>', null) //排除未完成运动
                            ->select(
                                DB::raw(
                                    'r_users.rid,
                                    count(r_runs.ruid) as count
                                    '
                                )
                            )
                            ->groupBy('r_runs.rid')
                            ->get();
        foreach($userRuns as $user){
            $hoid = 1;
            if($user->count <= 1) $hoid = 1;
            else if($user->count < 10) $hoid = 2;
            else if($user->count < 50) $hoid = 3;
            else if($user->count < 100) $hoid = 4;
            else if($user->count < 200) $hoid = 5;
            else if($user->count < 400) $hoid = 6;
            else if($user->count < 800) $hoid = 7;
            else if($user->count < 1000) $hoid = 8;
            else $hoid = 9;
            //先查询是否已存在相同等级的称号
            $linkHonors = LinkUHs::where('rid', $user->rid)->where('hoid', '=', $hoid)->first();
            if(!$linkHonors){ //如果不存在，则可以进行称号进阶授予
                LinkUHs::where('rid', $user->rid)
                        ->update(
                            [
                                'hoid' => $hoid
                            ]
                        );
                System::systemNotice([
                    'from' => 0, 
                    'to' => $user->rid, 
                    'type' => 0, 
                    'msg' => "你已累计运动 $user->count 次，授予您新的的称号: lv".$hoid
                ]);
            }
        }
    }
}
