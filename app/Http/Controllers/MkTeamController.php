<?php

namespace App\Http\Controllers;

use App\Models\MkTeam;
use Illuminate\Http\Request;
use \Validator;
use Illuminate\Http\Response;

class MkTeamController extends Controller
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
        $data = $request->json()->all();
        //is it repeat request?
        $mk_list_id = $data[0]['mk_list_id'];
        $result = MkTeam::where('mk_list_id', $mk_list_id)->count();
        if ($result) {
            $response = [
                "success" => false,
                'message' => 'The leaders has already stored'
            ];
            return response($response, 208);
        }

        $result = MkTeam::insert($data);
        if (!$result) {
            $response = [
                "success" => false,
                'message' => 'Requested data is not valid'
            ];
            return response($response, 208);
        }
        if ($result) {
            $response = [
                "success" => true,
                'message' => 'The leaders is stored'
            ];
            return response($response, 201);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MkTeams  $mkTeams
     * @return \Illuminate\Http\Response
     */
    public function show($mk_list_id)
    {
        //Empty test
        $result = MkTeam::where('mk_list_id', $mk_list_id)->count();
        if ($result == 0) {
            $response = [
                "success" => false,
                'message' => 'The team has not been created'
            ];
            return response($response, 404);
        }         

        //If data exist
        $result = MkTeam::where('mk_list_id', $mk_list_id)->
        join('users', 'users.id', '=', 'mk_teams.user_id')->
        get(['mk_teams.mk_list_id', 'mk_teams.user_id', 'mk_teams.items','mk_teams.role', 'users.name']);

        $response = [
                 "success" => true,
                 'message' => $result
             ];
             return response($response, 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MkTeams  $mkTeams
     * @return \Illuminate\Http\Response
     */
    public function edit(MkTeams $mkTeams)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MkTeams  $mkTeams
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MkTeams $mkTeams)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MkTeams  $mkTeams
     * @return \Illuminate\Http\Response
     */
    public function destroy($mk_list_id)
    {

        $result= MkTeam::where('mk_list_id', $mk_list_id)->delete();
        if (!$result) {
            $response = [
                "success" => false,
                'message' => 'The team is not found.'
            ];
            return response($response, 208);
        }
        if ($result) {
            $response = [
                "success" => true,
                'message' => 'The team is removed'
            ];
            return response($response, 201);
        }

    }
}
