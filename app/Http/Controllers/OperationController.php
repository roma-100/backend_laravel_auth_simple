<?php

namespace App\Http\Controllers;

//use App\Http\Requests\StoreOperationRequest;
//use App\Http\Requests\UpdateOperationRequest;
use App\Models\Operation;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //return Operation::orderBy('opdate', 'desc')->get();
       return Operation::orderBy('opdate', 'desc')->orderBy('id', 'desc')->take(50)->get();
    }

    public function total()
    {
        $data = [];
        $data['rur'] = Operation::all()->sum('rur');
        $data['usd'] = Operation::all()->sum('usd');
        $data['tg'] = Operation::all()->sum('tg');
        return $data;
        //return Operation::all();

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOperationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   //print_r($request['opdate']);
        $request->validate([
            'type' => 'required',
            //'name' => 'required',
            'description' => 'required',
            'opdate'=> 'required'
        ]);
        return Operation::create($request->all());
        //return Operation::create($request->take(10)->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        return Operation::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function edit(Operation $operation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $operation = Operation::find($id);
        $operation->update($request->all());
        return $operation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Operation::destroy($id);
    }

    /**
     * Search for a type
     *
     * @param  str $type
     * @return \Illuminate\Http\Response 
     * http://localhost:8000/api/operations/search/type 6
     */
    public function search($type)
    {
        return Operation::where('type', $type)->get();
        //return Operation::where('type', 'like', '%'.$type.'%')->get();
    }
}
