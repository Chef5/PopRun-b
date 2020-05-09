<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GrantSeasonMedal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:GrantSeasonMedal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '季成就（季度累计跑步达45次）：授予当季度勋章';

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
    }
}
