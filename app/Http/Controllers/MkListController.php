<?php

namespace App\Http\Controllers;

use App\Models\MkList;
use App\Models\MkStep;
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

        
        // $mk_list = MkList::join('mk_steps', 'mk_steps.mk_list_id', '=', 'mk_lists.id')->get(['mk_steps.*', 'mk_lists.*']);
        //return $mk_list;
    

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
    public function edit(MkList $mkList)
    {
        //
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
    public function destroy(MkList $mkList)
    {
        //
    }
}
