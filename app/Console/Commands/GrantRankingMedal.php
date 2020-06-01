<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\RRuns;
use App\RUsers;
use App\RMedals;
use App\LinkUMs;
use App\Http\Controllers\SystemController as System;

class GrantRankingMedal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GrantRankingMedal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '区域累计里程成就（月榜）：校区前100（rank_1.png），校区前10（rank_2.png），校前1（rank_3.png），国前100（rank_4.png）';

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
     * 勋章获取通知给用户
     */
    private function noticeUser($rid, $medalName){
        System::systemNotice([
            'to' => $rid, 
            'msg' => "你新获得一枚勋章<".$medalName.">"
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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
    }
}
