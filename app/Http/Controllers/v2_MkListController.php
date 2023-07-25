<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\MkList;
use App\Models\MkTeam;
use App\Models\UserStepMove;
use App\Models\UserStepFailed;
use App\Models\MkStep;
use App\Models\MkUserStateStep;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MkListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mk_list = MkList::orderBy('id', 'asc')->get();
        if (empty($mk_list)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $mk_list 
            ];
            return response($response, 201);
        }
    }

    public function mk_list_xd()
    {
        $sub_users = MkTeam::select('mk_list_id', DB::raw('COUNT(user_id) as stat_users'))
        ->groupBy('mk_list_id');
    }

    public function mk_list_steplers($mk_list_id)
    {
        $sub_leaders = MkTeam::where('mk_list_id', $mk_list_id);
        
        
        $users = User::select('users.id', 'users.name', 'users.position', 'users.role','user_id as leader', 'users.active')
        ->leftJoinSub($sub_leaders,'mk_teams',function($join){
            $join->on('users.id','=','mk_teams.user_id');
        })
        ->where('users.id', '>', 1)
        ->where('users.role', 'user')
        ->where('users.active', true)
        ->whereNull('mk_teams.user_id')
        ->orderBy('users.position')
        ->orderBy('users.name');



        $result = $users->get();
        if (empty($result)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $result 
            ];
            return response($response, 201);
        }
    }

    public function mk_list_complex($user_id)
    {
        //$user_id = 11;
        $role = DB::table('users') //select role
        -> where('id', $user_id)
        -> value('role');

        /* ==== Create relative queries === */
        $sub_team_users = MkTeam::select('mk_list_id', DB::raw('COUNT(user_id) as stat_users'))
        ->groupBy('mk_list_id');
        // Analyse role of user 
        $sub_team_user = MkTeam::select('mk_list_id', 'user_id', DB::raw('user_id as leader_id'))
        ->where('user_id',$user_id);

        $sub_steps = MkStep::select('mk_list_id', DB::raw('COUNT(step_num) as stat_steps'))
        ->groupBy('mk_list_id');
//adddd------
        $sub_steplers = MkStep::select('mk_list_id', DB::raw('COUNT(stepler_id) as count_stepler_id'))
        ->where('stepler_id', $user_id)
        ->groupBy('mk_list_id');
//adddd 2------
        $sub_steplers_step_handler = UserStepMove::select('mk_list_id', 'step_rx', DB::raw('SUM(items) as stepler_sum_items_step_rx'))
        ->groupBy('mk_list_id')
        ->groupBy('step_rx')
        ;        
        //join query
        $sub_steps_stepler = MkStep::select('mk_steps.id', 'mk_steps.mk_list_id', 
        'mk_steps.step_num', 'mk_steps.stepler_id', 'user_step_moves.step_rx', 
        'user_step_moves.stepler_sum_items_step_rx',
        DB::raw('mk_steps.stepler_id as found_stepler_id')
        )
        ->whereNotNull('stepler_id')
        ->where('stepler_id', $user_id)
        -> joinSub($sub_steplers_step_handler,'user_step_moves',function($join){
            $join->on('mk_steps.mk_list_id','=','user_step_moves.mk_list_id')
            ->on('mk_steps.step_num', '=', 'user_step_moves.step_rx');
        });

//end add 2
        $sub_moves = UserStepMove::select('mk_list_id', DB::raw('COUNT(mk_list_id) as stat_moves'))
        ->groupBy('mk_list_id');

        $sub_failed = UserStepFailed::select('mk_list_id', DB::raw('COUNT(mk_list_id) as stat_faileds'))
        ->groupBy('mk_list_id');

        $sub_users_state = MkUserStateStep::select('mk_list_id', 
        DB::raw('SUM(failed) as stat_sum_failed, SUM(handle) as stat_sum_handle, SUM(passed) as stat_sum_passed'))
        ->groupBy('mk_list_id');


            $query = MkList::orderBy('created_at', 'desc')
            ->leftJoinSub($sub_team_users,'mk_teams',function($join){
                $join->on('mk_lists.id','=','mk_teams.mk_list_id');
            })
            -> leftJoinSub($sub_team_user,'sub_team_user',function($join){ //user type test
                $join->on('mk_lists.id','=','sub_team_user.mk_list_id');
            })
            ->leftJoinSub($sub_steps,'mk_steps',function($join){
                $join->on('mk_lists.id','=','mk_steps.mk_list_id');
            })
//start stepler
            ->leftJoinSub($sub_steplers,'mk_steps_stepler',function($join){ //add
                $join->on('mk_lists.id','=','mk_steps_stepler.mk_list_id');
            })
            ->leftJoinSub($sub_steps_stepler,'sub_steps_stepler',function($join){ //add
                $join->on('mk_lists.id','=','sub_steps_stepler.mk_list_id');
            })
//end stepler
            ->leftJoinSub($sub_moves,'user_step_moves',function($join){
                $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
            })
            ->leftJoinSub($sub_failed,'user_step_faileds',function($join){
                $join->on('mk_lists.id','=','user_step_faileds.mk_list_id');
            })
            ->leftJoinSub($sub_users_state,'mk_user_state_steps',function($join){
                $join->on('mk_lists.id','=','mk_user_state_steps.mk_list_id');
            })
/*             ->leftJoinSub($sub_steplers,'mk_steps',function($join){//add
                $join->on('mk_lists.id','=','mk_steps.mk_list_id');
            })   */          
            ->select(['mk_lists.*','stat_users', 'stat_steps', 'stat_moves',
            'stat_sum_handle', 'stat_sum_passed', 'stat_sum_failed', 
            'mk_steps_stepler.count_stepler_id', 'stepler_sum_items_step_rx',// 'sub_steplers',
            'leader_id', 
                DB::raw('"'.$role.'" as info_user_role'),
                DB::raw('IF (found_stepler_id IS NOT NULL , true, false) as info_is_it_stepler'),
                DB::raw("
                IF (stat_users IS NULL OR stat_steps IS NULL, false, true) as is_team_and_steps
                "),
                DB::raw("
                IF (stat_sum_handle IS NULL AND stat_sum_passed IS NULL AND stat_sum_failed IS NULL, false, true) as is_mk_in_use
                ")]);        

        if ($role == 'admin'){
            $result = $query-> get();
        } else {
            // it was for leader only
            //it is show for leders and active steplers
            $result = $query
            ->whereRaw('stepler_sum_items_step_rx IS NOT NULL OR leader_id IS NOT NULL')
            -> get();
        } 

        //$r =  $result;
        $r=$sub_steps_stepler->get();
        //$r=$sub_steps_stepler->get();
        //$r=$sub_steplers_step_handler1;
        if (empty($r)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $r 
            ];
            return response($response, 201);
        }
    }       



    public function v1_mk_list_complex($user_id)
    {
        //$user_id = 11;
        $role = DB::table('users') //select role
        -> where('id', $user_id)
        -> value('role');

        /* ==== Create relative queries === */
        $sub_team_users = MkTeam::select('mk_list_id', DB::raw('COUNT(user_id) as stat_users'))
        ->groupBy('mk_list_id');
        // Analyse role of user 
        $sub_team_user = MkTeam::select('mk_list_id', 'user_id', DB::raw('user_id as leader_id'))
        ->where('user_id',$user_id);

        $sub_steps = MkStep::select('mk_list_id', DB::raw('COUNT(step_num) as stat_steps'))
        ->groupBy('mk_list_id');
//adddd------
        $sub_steplers = MkStep::select('mk_list_id', DB::raw('COUNT(stepler_id) as count_stepler_id'))
        ->where('stepler_id', $user_id)
        ->groupBy('mk_list_id');
//adddd 2------
        //$sub_steplers_step_handler = UserStepMove::select('mk_list_id', 'step_rx', DB::raw('SUM(items) as stepler_sum_items_step_rx'))
        
        $sub_steplers_step_handler = UserStepMove::select('mk_list_id', 'step_rx', DB::raw('SUM(items) as stepler_sum_items_step_rx'))
        -> groupBy('mk_list_id')
        -> groupBy('step_rx')
        
        ;        
        //join query
        $sub_steps_stepler = MkStep::select('mk_steps.id', 'mk_steps.mk_list_id', 
        'mk_steps.step_num', 'mk_steps.stepler_id', 'user_step_moves.step_rx', 
        'user_step_moves.stepler_sum_items_step_rx',
        DB::raw('mk_steps.stepler_id as found_stepler_id')
        )
        ->whereNotNull('stepler_id')
        ->where('stepler_id', $user_id)
        -> joinSub($sub_steplers_step_handler,'user_step_moves',function($join){
            $join->on('mk_steps.mk_list_id','=','user_step_moves.mk_list_id')
            ->on('mk_steps.step_num', '=', 'user_step_moves.step_rx');
        });


//end add 2
$sub_moves = UserStepMove::select('mk_list_id', DB::raw('COUNT(mk_list_id) as stat_moves'))
->groupBy('mk_list_id');

$sub_failed = UserStepFailed::select('mk_list_id', DB::raw('COUNT(mk_list_id) as stat_faileds'))
->groupBy('mk_list_id');

$sub_users_state = MkUserStateStep::select('mk_list_id', 
DB::raw('SUM(failed) as stat_sum_failed, SUM(handle) as stat_sum_handle, SUM(passed) as stat_sum_passed'))
->groupBy('mk_list_id');

/* ==== Create Main query === */
/*        $query = MkList::leftJoinSub($sub_team_users,'mk_teams',function($join){
    $join->on('mk_lists.id','=','mk_teams.mk_list_id');
})
->leftJoinSub($sub_steps,'mk_steps',function($join){
    $join->on('mk_lists.id','=','mk_steps.mk_list_id');
})
->leftJoinSub($sub_moves,'user_step_moves',function($join){
    $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
})
->leftJoinSub($sub_failed,'user_step_faileds',function($join){
    $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
})
->leftJoinSub($sub_users_state,'mk_user_state_steps',function($join){
    $join->on('mk_lists.id','=','mk_user_state_steps.mk_list_id');
})
->select(['mk_lists.*','stat_users', 'stat_steps', 'stat_moves', 
'stat_sum_handle', 'stat_sum_passed', 'stat_sum_failed', 
    DB::raw("
    IF (stat_users IS NULL OR stat_steps IS NULL, false, true) as is_team_and_steps
    "),
    DB::raw("
    IF (stat_sum_handle IS NULL AND stat_sum_passed IS NULL AND stat_sum_failed IS NULL, false, true) as is_mk_in_use
    ")]); */
//->groupByRaw('city, state')
    $query = MkList::orderBy('created_at', 'desc')
    ->leftJoinSub($sub_team_users,'mk_teams',function($join){
        $join->on('mk_lists.id','=','mk_teams.mk_list_id');
    })
    -> leftJoinSub($sub_team_user,'sub_team_user',function($join){ //user type test
        $join->on('mk_lists.id','=','sub_team_user.mk_list_id');
    })
    ->leftJoinSub($sub_steps,'mk_steps',function($join){
        $join->on('mk_lists.id','=','mk_steps.mk_list_id');
    })
//start stepler
    ->leftJoinSub($sub_steplers,'mk_steps_stepler',function($join){ //add
        $join->on('mk_lists.id','=','mk_steps_stepler.mk_list_id');
    })
    ->leftJoinSub($sub_steps_stepler,'sub_steps_stepler',function($join){ //add
        $join->on('mk_lists.id','=','sub_steps_stepler.mk_list_id');
    })
//end stepler
    ->leftJoinSub($sub_moves,'user_step_moves',function($join){
        $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
    })
    ->leftJoinSub($sub_failed,'user_step_faileds',function($join){
        $join->on('mk_lists.id','=','user_step_faileds.mk_list_id');
    })
    ->leftJoinSub($sub_users_state,'mk_user_state_steps',function($join){
        $join->on('mk_lists.id','=','mk_user_state_steps.mk_list_id');
    });     
    //select fields   
        $aFields = ['mk_lists.id','mk_lists.name','mk_lists.quantity','mk_lists.description',
        'mk_lists.date_start', 'mk_lists.date_finish', 'mk_lists.user_id',
        'mk_lists.active','mk_lists.created_at','mk_lists.updated_at',
        'stat_users','stat_steps','stat_moves','stat_sum_handle',
        'stat_sum_passed','stat_sum_failed','count_stepler_id',
        'leader_id', 
            DB::raw('"'.$role.'" as info_user_role'),
            DB::raw('IF (found_stepler_id IS NOT NULL , true, false) as info_is_it_stepler'),
            DB::raw("
            IF (stat_users IS NULL OR stat_steps IS NULL, false, true) as is_team_and_steps
            "),
            DB::raw("
            IF (stat_sum_handle IS NULL AND stat_sum_passed IS NULL AND stat_sum_failed IS NULL, false, true) as is_mk_in_use
            ")
        ];

        $aGFields = ['mk_lists.id','mk_lists.name','mk_lists.quantity','mk_lists.description',
        'mk_lists.date_start', 'mk_lists.date_finish', 'mk_lists.user_id',
        'mk_lists.active','mk_lists.created_at','mk_lists.updated_at',
        'stat_users','stat_steps','stat_moves','stat_sum_handle',
        'stat_sum_passed','stat_sum_failed','count_stepler_id',
        'leader_id'];

        $queryOut = $query
        ->select ($aFields)
        ->groupBy($aGFields);

if ($role == 'admin'){
    $result = $query-> get();
} else {
    // it was for leader only
    //it is show for leders and active steplers
    $result = $query
    ->whereRaw('stepler_sum_items_step_rx IS NOT NULL OR leader_id IS NOT NULL')
    -> get();
} 



        $r=$result;
/*         $r=$query
        ->select ($aFields)
        ->groupBy($aGFields)
        ->get(); */
/*         $r=$sub_steplers_step_handler
        ->get(); */
        
        if (empty($r)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $r 
            ];
            return response($response, 201);
        }
    }        
     

    public function mk_list_complex1($user_id)
    {
        //$user_id = 11;
        $role = DB::table('users') //select role
        -> where('id', $user_id)
        -> value('role');

        /* ==== Create relative queries === */
        $sub_team_users = MkTeam::select('mk_list_id', DB::raw('COUNT(user_id) as stat_users'))
        ->groupBy('mk_list_id');
        // Analyse role of user 
        $sub_team_user = MkTeam::select('mk_list_id', 'user_id', DB::raw('user_id as leader_id'))
        ->where('user_id',$user_id);

        $sub_steps = MkStep::select('mk_list_id', DB::raw('COUNT(step_num) as stat_steps'))
        ->groupBy('mk_list_id');
//adddd------
        $sub_steplers = MkStep::select('mk_list_id', DB::raw('COUNT(stepler_id) as count_stepler_id'))
        ->where('stepler_id', $user_id)
        ->groupBy('mk_list_id');
//adddd 2------
        $sub_steplers_step_handler = UserStepMove::select('mk_list_id', 'step_rx', DB::raw('SUM(items) as stepler_sum_items_step_rx'))
        ->groupBy('mk_list_id')
        ->groupBy('step_rx')
        ;        
        //join query
        $sub_steps_stepler = MkStep::select('mk_steps.id', 'mk_steps.mk_list_id', 
        'mk_steps.step_num', 'mk_steps.stepler_id', 'user_step_moves.step_rx', 
        'user_step_moves.stepler_sum_items_step_rx',
        DB::raw('mk_steps.stepler_id as found_stepler_id')
        )
        ->whereNotNull('stepler_id')
        ->where('stepler_id', $user_id)
        -> joinSub($sub_steplers_step_handler,'user_step_moves',function($join){
            $join->on('mk_steps.mk_list_id','=','user_step_moves.mk_list_id')
            ->on('mk_steps.step_num', '=', 'user_step_moves.step_rx');
        });

//end add 2
        $sub_moves = UserStepMove::select('mk_list_id', DB::raw('COUNT(mk_list_id) as stat_moves'))
        ->groupBy('mk_list_id');

        $sub_failed = UserStepFailed::select('mk_list_id', DB::raw('COUNT(mk_list_id) as stat_faileds'))
        ->groupBy('mk_list_id');

        $sub_users_state = MkUserStateStep::select('mk_list_id', 
        DB::raw('SUM(failed) as stat_sum_failed, SUM(handle) as stat_sum_handle, SUM(passed) as stat_sum_passed'))
        ->groupBy('mk_list_id');

        /* ==== Create Main query === */
 /*        $query = MkList::leftJoinSub($sub_team_users,'mk_teams',function($join){
            $join->on('mk_lists.id','=','mk_teams.mk_list_id');
        })
        ->leftJoinSub($sub_steps,'mk_steps',function($join){
            $join->on('mk_lists.id','=','mk_steps.mk_list_id');
        })
        ->leftJoinSub($sub_moves,'user_step_moves',function($join){
            $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
        })
        ->leftJoinSub($sub_failed,'user_step_faileds',function($join){
            $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
        })
        ->leftJoinSub($sub_users_state,'mk_user_state_steps',function($join){
            $join->on('mk_lists.id','=','mk_user_state_steps.mk_list_id');
        })
        ->select(['mk_lists.*','stat_users', 'stat_steps', 'stat_moves', 
        'stat_sum_handle', 'stat_sum_passed', 'stat_sum_failed', 
            DB::raw("
            IF (stat_users IS NULL OR stat_steps IS NULL, false, true) as is_team_and_steps
            "),
            DB::raw("
            IF (stat_sum_handle IS NULL AND stat_sum_passed IS NULL AND stat_sum_failed IS NULL, false, true) as is_mk_in_use
            ")]); */
//->groupByRaw('city, state')
            $query = MkList::orderBy('created_at', 'desc')
            ->leftJoinSub($sub_team_users,'mk_teams',function($join){
                $join->on('mk_lists.id','=','mk_teams.mk_list_id');
            })
            -> leftJoinSub($sub_team_user,'sub_team_user',function($join){ //user type test
                $join->on('mk_lists.id','=','sub_team_user.mk_list_id');
            })
            ->leftJoinSub($sub_steps,'mk_steps',function($join){
                $join->on('mk_lists.id','=','mk_steps.mk_list_id');
            })
//start stepler
            ->leftJoinSub($sub_steplers,'mk_steps_stepler',function($join){ //add
                $join->on('mk_lists.id','=','mk_steps_stepler.mk_list_id');
            })
            ->leftJoinSub($sub_steps_stepler,'sub_steps_stepler',function($join){ //add
                $join->on('mk_lists.id','=','sub_steps_stepler.mk_list_id');
            })
//end stepler
            ->leftJoinSub($sub_moves,'user_step_moves',function($join){
                $join->on('mk_lists.id','=','user_step_moves.mk_list_id');
            })
            ->leftJoinSub($sub_failed,'user_step_faileds',function($join){
                $join->on('mk_lists.id','=','user_step_faileds.mk_list_id');
            })
            ->leftJoinSub($sub_users_state,'mk_user_state_steps',function($join){
                $join->on('mk_lists.id','=','mk_user_state_steps.mk_list_id');
            })
/*             ->leftJoinSub($sub_steplers,'mk_steps',function($join){//add
                $join->on('mk_lists.id','=','mk_steps.mk_list_id');
            })   */          
            ->select(['mk_lists.*','stat_users', 'stat_steps', 'stat_moves',
            'stat_sum_handle', 'stat_sum_passed', 'stat_sum_failed', 
            'mk_steps_stepler.count_stepler_id', 'stepler_sum_items_step_rx',// 'sub_steplers',
            'leader_id', 
                DB::raw('"'.$role.'" as info_user_role'),
                DB::raw('IF (found_stepler_id IS NOT NULL , true, false) as info_is_it_stepler'),
                DB::raw("
                IF (stat_users IS NULL OR stat_steps IS NULL, false, true) as is_team_and_steps
                "),
                DB::raw("
                IF (stat_sum_handle IS NULL AND stat_sum_passed IS NULL AND stat_sum_failed IS NULL, false, true) as is_mk_in_use
                ")]);        

        if ($role == 'admin'){
            $result = $query-> get();
        } else {
            // it was for leader only
            //it is show for leders and active steplers
            $result = $query
            ->whereRaw('stepler_sum_items_step_rx IS NOT NULL OR leader_id IS NOT NULL')
            -> get();
        } 

        $r =  $result;
        //$r=$sub_steplers_step_handler->get();
        //$r=$sub_steps_stepler->get();
        //$r=$sub_steplers_step_handler1;
        if (empty($r)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $r 
            ];
            return response($response, 201);
        }
    }        
        // $mk_list = MkList::join('mk_steps', 'mk_steps.mk_list_id', '=', 'mk_lists.id')->get(['mk_steps.*', 'mk_lists.*']);
        //return $mk_list;
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mk_list_add_mk(Request $request)
    {
        $data = $request->all();
        $result = MkList::insert($data);
        if ($result == 1) {
            $response = [
                "success" => true,
                'message' => 'Data has recorded'
            ];
            return response($response, 201);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MkList  $mkList
     * @return \Illuminate\Http\Response
     */
    public function show(MkList $mkList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MkList  $mkList
     * @return \Illuminate\Http\Response
     */
    public function mk_list_edit_mk(Request $request)
    {
        //
        $result_update = MkList::where('id', $request['id']) 
        ->update(['name' => $request['name'], 
                  'quantity' => $request['quantity'],
                  'description' => $request['description'],
                  //'active' => $request['active'],
                  //'tmp' => DB::raw('SELECT id FROM mk_user_state_steps WHERE mk_list_id = 1 AND user_id = 10 AND step_num = 4')[0]
                ]);

                if ($result_update == 1) {
                    $response = [
                        "success" => true,
                        'message' => 'Data has updated'
                    ];
                    return response($response, 201);
                } else {
                    $response = [
                        "success" => false,
                        'message' => 'Something went wrong...'
                    ];
                    return response($response, 503);                    
                }               
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MkList  $mkList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MkList $mkList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MkList  $mkList
     * @return \Illuminate\Http\Response
     */
    public function destroy($mk_list_id)
    {
        $result = MkList::destroy($mk_list_id);

        if ($result == 1) {
            $response = [
                "success" => true,
                'message' => 'Data has deleted'
            ];
            return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'Data not found'
            ];
            return response($response, 404);            
        }
    }

    public function mk_list_delete_all_steps($mk_list_id)
    {
        $result = MkStep:: where('mk_list_id', $mk_list_id)
        -> delete();

        if ($result == 1) {
            $response = [
                "success" => true,
                'message' => 'All steps have deleted'
            ];
            return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'Data not found'
            ];
            return response($response, 404);            
        }
    }

}
