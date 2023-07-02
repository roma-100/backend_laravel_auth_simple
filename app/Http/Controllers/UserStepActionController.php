<?php

namespace App\Http\Controllers;
use App\Models\MkTeam;
use App\Models\UserStepMove;
use App\Models\UserStepFailed;
use App\Models\MkStep;
use App\Models\MkUserStateStep;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserStepActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function store_mk_user_steps_move(Request $request)
    {
        /* UserStepMove */
        $data = $request->json()->all();
        $result = UserStepMove::insert($data);
            if ($result == 1) {
            $response = [
                "success" => true,
                'message' => 'Data has recorded'
            ];
            return response($response, 201);
        }
    }

    public function mk_user_steps_failed(Request $request)
    {
        /* UserStepMove */
        $data = $request->json()->all();
        $result = UserStepFailed::insert($data);
            if ($result == 1) {
            $response = [
                "success" => true,
                'message' => 'Data has recorded'
            ];
            return response($response, 201);
        }
    }    
//=============== START mk_user_state_steps_get_smart ==========================
    public function mk_user_state_steps_get_smart($mk_list_id, $user_id) //$mk_list_id, $user_id
    {   
        /* Algoritm:
            if null data -> create table from table mk_steps
            if no moves -> it is initial state -> hide all radiobutton except the first
        */

        $found = MkUserStateStep::where('mk_list_id', $mk_list_id) 
        -> where('user_id', $user_id)
        -> count();

        // init process test
        //*******if null data -> create table from table mk_steps ******
        if ($found == 0) {
            // Create init process
            $this -> smart_init_mode_insert_data($mk_list_id, $user_id);           
        }
        //............ Analysis .......................
            // test init mode and show only first radiobutton
            // test init Mode included
            $this -> smart_init_mode_show_only_first_radiobutton($mk_list_id, $user_id);

            // Move mode show default value -> number field & radiobutton display
            // Test not init mode included
            $this ->  smart_move_mode_display_nmber_fields_and_radiobuttons($mk_list_id, $user_id);

        //......... Get data ....................
        // The records defenetly exist
        //Join data
        //$result = MkUserStateStep::where('mk_list_id', $mk_list_id) -> get()-> sortBy('step_num');
        $result = DB::table('mk_user_state_steps')
        -> join('mk_steps', function($q)
        {
            $q->on('mk_steps.mk_list_id', '=', 'mk_user_state_steps.mk_list_id')
                ->on('mk_steps.step_num', '=', 'mk_user_state_steps.step_num');
        })
        -> where('mk_user_state_steps.mk_list_id', $mk_list_id)
        -> where('mk_user_state_steps.user_id', $user_id)
        -> get(['mk_user_state_steps.*',
        'mk_steps.action', 'mk_steps.description', 'mk_steps.duration' ])
        -> sortBy('step_num');

        //............. Response part ...................
        if ($result) {
            $response = [
                "success" => true,
                'message' => $result
            ];
            return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'Error mk_user_state_steps'
            ];
            return response($response, 400);
        }
   

    }

    private function smart_init_mode_insert_data($mk_list_id, $user_id){
        //$result = MkStep::select('step_num', 'mk_list_id', 'ddd')->where('mk_list_id', $request['mk_list_id']) -> get()->sortBy('step_num');
        $result = MkStep::select('mk_list_id', DB::raw($user_id . " as user_id"), 'step_num' )
        ->where('mk_list_id', $mk_list_id) -> get()-> sortBy('step_num');
        //$result is the object

        //Let's transform the object to array
        $result = json_decode(json_encode($result), true);

        //Insert data to the table
        $result = MkUserStateStep::insert($result); 
    } 
    private function smart_init_mode_show_only_first_radiobutton($mk_list_id, $user_id){
        //....test move action....
        $result = UserStepMove::where('mk_list_id', $mk_list_id)
        -> where('user_id', $user_id)
        -> count();
        if ($result == 0) {
           // $result2 = DB::table('mk_user_state_steps')->get();
            $result2 = MkUserStateStep::where('mk_list_id', $mk_list_id)
            -> where('user_id', $user_id)
            -> where('step_num', '>', 1)
            -> update(['radiohidden' => true]);
        } 

    }
    private function smart_move_mode_display_nmber_fields_and_radiobuttons($mk_list_id, $user_id){
        //....test move action....
        $result = UserStepMove::where('mk_list_id', $mk_list_id)
        -> where('user_id', $user_id)
        -> count();
        if ($result > 0) { // Test Move existence
           // $result2 = DB::table('mk_user_state_steps')->get();
           // se hidden all data: radiohidden numbhidden
            $result2 = MkUserStateStep::where('mk_list_id', $mk_list_id)
            -> where('user_id', $user_id)
            //-> where('step_num', '>', 1)
            -> update(['radiohidden' => false, 'numbhidden' => true]);

            $result3 = MkUserStateStep::where('mk_list_id', $mk_list_id)
            -> where('user_id', $user_id)
            -> where('handle', '>', 0)
            -> update(['numbhidden' => false]);           

            //if it is only one handler -> hide radiobutton
            $result4 = MkUserStateStep::where('mk_list_id', $mk_list_id)
            -> where('user_id', $user_id)
            -> where('handle', '>', 0)
            -> count();
            if ($result4 == 1){
                $result5 = MkUserStateStep::where('mk_list_id', $mk_list_id)
                -> where('user_id', $user_id)
                -> where('handle', '>', 0)
                -> update(['radiohidden' => true, 'numbhidden' => false]);
            }
        }
    }
//====================== END mk_user_state_steps_get_smart ====================================

    public function mk_user_state_steps_update(Request $request) 
    /* 
    {$mk_list_id,
     $user_id,
     step_num,

     handle, passed, failed
    }*/
    {
        // test exist the record
        $found = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> where('step_num', $request['step_num'])
        -> count();
        if ($found == 0) {
            $response = [
                "success" => false,
                'message' => 'mk_user_state_steps not found. mk_list_id:'
                . $request['mk_list_id'] . ', user_id:' . $request['user_id']
            ];
            return response($response, 404);
        }

        //... Start Analyse handle_at ....   
        // Create this step handle_at if it needs (handle=0)
        $result = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> where('step_num', $request['step_num'])
        -> where('handle', 0)
        -> where('passed', 0)
        -> where('failed', 0)
        ->update(['handle_at' => now()
                ]);
        //... End Analyse handle_at ....   

        //--- New reality data ---
        //Find the record and update  
        $result_update = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> where('step_num', $request['step_num'])
        ->update(['handle' => $request['handle'], 
                  'passed' => $request['passed'],
                  'failed' => $request['failed'],
                  //'tmp' => DB::raw('SELECT id FROM mk_user_state_steps WHERE mk_list_id = 1 AND user_id = 10 AND step_num = 4')[0]
                ]);
        //--- End New reality data ---
       
        //.... Start Analyse done_at ....
        //get items data and failed
        //it must be userItems = userPassedStep + failed
        $result = MkTeam::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> get('items'); 
        $mk_list_user_items = $result[0]['items'];
        //print_r ($result);   
        //echo $user_items. "<br>";  
        // get user failed sum
        $result = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> select(DB::raw("SUM(failed) as total_failed"))
        -> get();  
        $mk_list_user_total_failed = $result[0]['total_failed'];

        //echo $result[0]['total_failed'] . "<br>";

        // Create this step done_at if it needs (handle =0 || passed>0 || failed )
        //if no -> set Null
        $diff = $mk_list_user_items - $mk_list_user_total_failed - $request['passed'];
        if ($request['handle'] == 0 && $diff == 0) {
            $result = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
            -> where('user_id', $request['user_id'])
            -> where('step_num', $request['step_num'])
            ->update(['done_at' => now()
                    ]);
        }       

        $result = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> where('step_num', $request['step_num'])
        -> where('handle', '>', 0)
        ->update(['done_at' => null
                ]);
        //.... End Analyse done_at ....

        if ($result_update) {
            $response = [
                "success" => true,
                'message' => 'mk_user_state_steps updated. mk_list_id:'
                . $request['mk_list_id'] . ', user_id:' . $request['user_id'] . ', step_num:' . $request['step_num']
            ];
            return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'mk_user_state_step is not updated.'
            ];
            return response($response, 500);
        }
        
    }

    //This is indicator for initial step
    public function mk_user_is_initial($mk_list_id, $user_id) //$mk_list_id, $user_id
    {
        $result = UserStepMove::where('mk_list_id', $mk_list_id) ->
        where('user_id', $user_id)->count();

        if ($result == 0) {
            $response = [
                "success" => true,
                'message' => 'No records. It is the initia state.'
            ];
            return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'The records exist. It is not the initia state.' 
            ];
            return response($response, 201);
        } 
    }

    public function mk_user_state_steps_initial(Request $request) //$mk_list_id, $user_id 
    {
        //get from $request mk_list_id & user_id
        /* 
        $request: {
            mk_list_id
            user_id
        }
        */
        //Stop duplicate data
        $found = MkUserStateStep::where('mk_list_id', $request['mk_list_id']) 
        -> where('user_id', $request['user_id'])
        -> count();
        if (!$found) {
            //MkStep
            $mk_list_id = $request['mk_list_id'];
            $user_id = $request['user_id'];
            //$result = MkStep::select('step_num', 'mk_list_id', 'ddd')->where('mk_list_id', $request['mk_list_id']) -> get()->sortBy('step_num');
            $result = MkStep::select('mk_list_id', DB::raw($user_id . " as user_id"), 'step_num' )
            ->where('mk_list_id', $mk_list_id) -> get()-> sortBy('step_num');
            //$result is the object

            //Let's transform the object to array
            $result = json_decode(json_encode($result), true);

            //Insert data to the table
            $result = MkUserStateStep::insert($result);

            //print_r($result);
            //echo $result; 
            if ($result) {
                $response = [
                    "success" => true,
                    'message' => 'mk_user_state_steps_initial created. mk_list_id:'
                    . $request['mk_list_id'] . ', user_id:' . $request['user_id']
                ];
                return response($response, 201);
            } else {
                $response = [
                    "success" => false,
                    'message' => 'mk_user_state_steps_initial not created. mk_list_id:'
                    . $request['mk_list_id'] . ', user_id:' . $request['user_id']
                ];
                return response($response, 500);
            }

        } else {
            $response = [
                "success" => false,
                'message' => 'mk_user_state_steps_initial was created before. mk_list_id:'
                . $request['mk_list_id'] . ', user_id:' . $request['user_id']
            ];
            return response($response, 404);
        }

        


        //return response($result, 201);
    }

    public function mk_user_state_steps_destroy($mk_list_id, $user_id)
    {
        //Delelete mk_user_state_steps
        $result = MkUserStateStep::where('mk_list_id', $mk_list_id) 
        -> where('user_id', $user_id)
        -> delete();

        if ($result > 0) {
            $response = [
                "success" => true,
                'message' => 'mk_user_state_steps has deleted. mk_list_id: '
                . $mk_list_id . ', user_id:' . $user_id
            ];
            return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'mk_user_state_steps not found. mk_list_id:'
                . $mk_list_id . ', user_id:' . $user_id 
            ];
            return response($response, 404);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /*     public function destroy($mk_list_id, $user_id)
    {

        $result= MkTeam::where('mk_list_id', $mk_list_id)->delete();
        
        mk_user_state_steps_drop/{mk_list_id}/{user_id}
        */


        public function mk_user_state_steps_get_simple($mk_list_id, $user_id) //$mk_list_id, $user_id
        {
    
            $found = MkUserStateStep::where('mk_list_id', $mk_list_id) 
            -> where('user_id', $user_id)
            -> count();
    
            if ($found) {
                //MkStep
                //$result = MkStep::select('step_num', 'mk_list_id', 'ddd')->where('mk_list_id', $request['mk_list_id']) -> get()->sortBy('step_num');
                $result = MkStep::where('mk_list_id', $mk_list_id) -> get()-> sortBy('step_num');
                //$result is the object
                //echo $result; 
                if ($result) {
                    $response = [
                        "success" => true,
                        'message' => $result
                    ];
                    return response($response, 201);
                }
            }    
            if ($found == 0) {
                    $response = [
                        "success" => false,
                        'message' => 'mk_user_state_steps_initial not found. mk_list_id:'
                        . $mk_list_id . ', user_id:' . $user_id
                    ];
                    return response($response, 404);
            }
        }      
        
        public function test(){
            echo "hello";
        }
   
}
