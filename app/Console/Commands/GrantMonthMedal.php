<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\RRuns;
use App\RUsers;
use App\RMedals;
use App\LinkUMs;
use App\Http\Controllers\SystemController as System;


class GrantMonthMedal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GrantMonthMedal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '月成就（月累计跑步达7次）：授予当月勋章';

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
        if($medal){
            foreach($userRuns as $user){
                if($user->count >= 7){
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
        }
    }
}
