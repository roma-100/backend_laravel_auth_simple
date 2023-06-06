<?php

namespace App\Http\Controllers;
/* use App\Http\Requests\EditUserRequest; */
use App\Models\MkStep;
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
        /* $finder = MkStep::find($mk_list_id); */
        $finder = MkStep::whereIn('mk_list_id', [$mk_list_id])->orderBy('step_num', 'asc')->get();

        if (empty($finder)) {
            $response = [
                "success" => false,
                'message' => 'Null data'
            ];
            return response($response, 404);
        } else {
            $response = [
                "success" => true,
                'message' => $finder 
            ];
            return response($response, 201);
        }
    }

/* return Operation::find($id); */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* id // auto counter
        mk_list_id 
        step_num 
        action 
        description 
        duration */

        /* MkStep::create($request->all()); */
        //$steps = MkStep::where('id', $request['id'])->update($request->all());

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
         $response = MkStep::create($request->all());

       $test = $response;
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
        //RRR09 it must be cleared
        /* print_r($validator->errors()); */
        /* return response($validator->errors(), 501); */
        /* $this->validateWith($validator,$request); */

        //Validation test
/*         $fields = $request->validate([
            'description' => 'required|string'
        ]); */
        /* $step = MkStep::where('id', $request['id'])->first(); */

        //Find th record
        /* $operation = MkStep::find($request['id']); */
        /* $operation->update($fields->all()); */

        /* $stepNum = $request['step_num']; */

        /* $operation = MkStep::find($request['id']); */

       /* print_r($fields); */
/*        echo "<p> Look: ".$request['step_num']. "</p>";
       echo "<p> Look: ".$step['description']. "</p>"; */
       /* print_r($operation); */

/*        $response = [
        "success" => true,
        'message' => 'ggg'
        ]; */

         /* return response($response, 201); */
        /* return $request.step_num; */
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
}
