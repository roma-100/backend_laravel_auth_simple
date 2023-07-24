<?php

namespace App\Http\Controllers;
/* use App\Http\Requests\EditUserRequest; */

use Illuminate\Support\Facades\DB;
use App\Models\MkStep;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\EditMkStepRequest;
use \Validator;
use Illuminate\Http\Response;

class MkStepController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mk_step_show($mk_list_id)
    {
 /*        $subquery1 = DB::table('mk_user_state_steps')
        ->select(['mk_list_id', 'step_num', 'handle', 'passed', 'failed', 
        DB::raw("
        IF (handle + passed + failed = 0, true, false) as is_allow_delete
        ")]); */

        /* IF (stat_users IS NULL OR stat_steps IS NULL, false, true)
        $finder = MkStep::find($mk_list_id); , 'handle', 'passed', 'failed' , DB::raw('MIN(step_tx) as steps_tx')*/
       // $finder = MkStep::whereIn('mk_list_id', [$mk_list_id])->orderBy('step_num', 'asc')->get();
       $sub_users = User::select('*');

       $this->mk_list_users_state_steps_to_table_steps($mk_list_id);

/*        $query = MkStep::select(['mk_steps.*', DB::raw("
       IF (tmp_handle + tmp_passed + tmp_failed = 0, true, false) as is_allow_delete
       ")])
        -> where('mk_steps.mk_list_id', '=', $mk_list_id)
        ->get();
 */

       $query = MkStep::select(['mk_steps.*', DB::raw("
       IF (tmp_handle + tmp_passed + tmp_failed = 0, true, false) as is_allow_delete
       "), 'users.name as stepler_name'])
        -> where('mk_steps.mk_list_id', '=', $mk_list_id)
       ->leftJoinSub($sub_users,'users',function($join){
           $join->on('mk_steps.stepler_id','=','users.id');
       })
       ->get();

        //$query = MkStep::all()->sortBy('step_num')->where('mk_list_id', $mk_list_id);
//MkStep::all()->sortBy('step_num')->where('mk_list_id', $mk_list_id);
        $result = $query;
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

    public function xd_mk_step_show($mk_list_id)
    {
        $subquery1 = DB::table('mk_user_state_steps')
        ->select(['mk_list_id', 'step_num', 'handle', 'passed', 'failed', 
        DB::raw("
        IF (handle + passed + failed = 0, true, false) as is_allow_delete
        ")]);

        /* IF (stat_users IS NULL OR stat_steps IS NULL, false, true)
        $finder = MkStep::find($mk_list_id); , 'handle', 'passed', 'failed' , DB::raw('MIN(step_tx) as steps_tx')*/
       // $finder = MkStep::whereIn('mk_list_id', [$mk_list_id])->orderBy('step_num', 'asc')->get();

       $query = MkStep::leftJoinSub($subquery1,'mk_user_state_steps',function($join){
        $join->on('mk_steps.mk_list_id','=','mk_user_state_steps.mk_list_id')
        ->on('mk_steps.step_num','=','mk_user_state_steps.step_num');
        })
        ->select(['mk_steps.*','is_allow_delete'])
        -> where('mk_steps.mk_list_id', '=', $mk_list_id)
        ->get();

        $result = $query;
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

/* return Operation::find($id); */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Duplicate test Errorr 409
        $duplicate = MkStep::where('mk_list_id', $request['mk_list_id']) ->
                    where('step_num', $request['step_num'])->count();
        if ($duplicate) {
            $response = [
                "success" => false,
                'message' => 'The step already exists'
            ];
            return response($response, 409);
        }    
        // Add operation
         //$response = MkStep::create($request->all());

         $data = $request->all();
         //print_r($request->all());
         $result = MkStep::insert($data);

       $test = $result;
        if (empty($test)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => 'The step is created' 
            ];
            return response($response, 201);
        }       
        return 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $steps = MkStep::where('id', $request['id'])->update($request->all());
        /* $steps->update($request->all()); */

        $test = $steps;
        if (empty($test)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $test 
            ];
            return response($response, 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request['id'];
        $result=MkStep::destroy($id);
        if ($result) {
        $response = [
            "success" => true,
            'message' => 'Step deleted'
        ]; 
        return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'Not found step #' .$id
            ];   
        return response($response, 404);          
        }
    }
    
    /* /mk_list_import_steps/{mk_list_id_rx}/{mk_list_id_tx}' */
    public function mk_list_import_steps($mk_list_id_tx, $mk_list_id_rx)
    {
        /* Start testing mk_list_id_tx  */
        $steps = MkStep::select( DB::raw('COUNT(mk_list_id) as count_steps'))
        -> where('mk_list_id', $mk_list_id_tx);
        //->value('count_steps');
        
        if ($steps->value('count_steps') == 0) {
            $response = [
                "success" => false,
                'message' => 'Not Found mk_list_id_tx'
            ];
            return response($steps->get(), 404);
        } 

        /* End testing mk_list_id_tx  */

        /* Delete  old mk_list_id_rx from steps*/
        $result = MkStep::where('mk_list_id', $mk_list_id_rx)->delete();

        /* Isert new data from another steps */
        $result = MkStep::select(['step_num', 'action', 'description', 'duration', 'stepler_id', DB::raw($mk_list_id_rx . " as mk_list_id")])
        -> where('mk_list_id', $mk_list_id_tx)
        ->get()-> sortBy('step_num'); 

        $data = json_decode(json_encode($result), true);

        //Insert data to the table
        $result = MkStep::insert($data);

        $r =  $result;
        if (empty($r)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => 'Steps imported'
            ];
            return response($response, 201);
        }
    }

    public function mk_list_join_users_steps($mk_list_id)
    {
        //get join users date from mk_user_state_steps

        $this->mk_list_users_state_steps_to_table_steps($mk_list_id);
/*         $query_users_steps = DB::table('mk_user_state_steps')
        -> select('mk_list_id', 'step_num', 
                    DB::raw('SUM(handle) as sum_handle'), 
                    DB::raw('SUM(passed) as sum_passed'), 
                    DB::raw('SUM(failed) as sum_failed'),
                    DB::raw('SUM(done) as sum_done'))
        ->where('mk_list_id', $mk_list_id)
        ->groupBy('mk_list_id','step_num');

        $result = MkStep::leftJoinSub($query_users_steps,'mk_user_state_steps',function($join){
            $join->on('mk_steps.mk_list_id','=','mk_user_state_steps.mk_list_id')
            ->on('mk_steps.step_num','=','mk_user_state_steps.step_num');
        })
        ->where('mk_steps.mk_list_id', $mk_list_id)
        ->select('mk_steps.*', 'sum_handle', 'sum_passed', 'sum_failed', 'sum_done')
        //->get();
        ->update(['tmp_handle' => DB::raw("IF (sum_handle IS NULL, 0, sum_handle)"),
                  'tmp_passed' => DB::raw("IF (sum_passed IS NULL, 0, sum_passed)"),
                  'tmp_failed' => DB::raw("IF (sum_failed IS NULL, 0, sum_failed)"),
                  'tmp_done' => DB::raw("IF (sum_done IS NULL, 0, sum_done)"),
        ]); */

        //echo "<pre>"; print_r($result); echo "</pre>";

        // get fresh data steps
        $result = MkStep::where('mk_list_id', $mk_list_id)->get();
        //->sortBy('step_num')
        //->get();
        $r =  $result;
        if (empty($r)) {
            $response = [
                "success" => false,
                'message' => 'Error. The mk_list_id does not exist...'
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

    private function mk_list_users_state_steps_to_table_steps($mk_list_id){
        $query_users_steps = DB::table('mk_user_state_steps')
        -> select('mk_list_id', 'step_num', 
                    DB::raw('SUM(handle) as sum_handle'), 
                    DB::raw('SUM(passed) as sum_passed'), 
                    DB::raw('SUM(failed) as sum_failed'),
                    DB::raw('SUM(done) as sum_done'))
        ->where('mk_list_id', $mk_list_id)
        ->groupBy('mk_list_id','step_num');

        $result = MkStep::leftJoinSub($query_users_steps,'mk_user_state_steps',function($join){
            $join->on('mk_steps.mk_list_id','=','mk_user_state_steps.mk_list_id')
            ->on('mk_steps.step_num','=','mk_user_state_steps.step_num');
        })
        ->where('mk_steps.mk_list_id', $mk_list_id)
        ->select('mk_steps.*', 'sum_handle', 'sum_passed', 'sum_failed', 'sum_done')
        //->get();
        ->update(['tmp_handle' => DB::raw("IF (sum_handle IS NULL, 0, sum_handle)"),
                  'tmp_passed' => DB::raw("IF (sum_passed IS NULL, 0, sum_passed)"),
                  'tmp_failed' => DB::raw("IF (sum_failed IS NULL, 0, sum_failed)"),
                  'tmp_done' => DB::raw("IF (sum_done IS NULL, 0, sum_done)"),
        ]); 
    }
}
