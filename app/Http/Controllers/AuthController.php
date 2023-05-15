<?php

namespace App\Http\Controllers;
use App\Http\Requests\EditUserRequest;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register (Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            "success" => true,
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function edit_user (EditUserRequest $request, $id) {

        
        if ($id > 1) {  
         $user = User::find($id)->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password'])
        ]);

        $response = [
            "success" => true,
            'message' => 'user #' .$id. ' updated'
        ]; 
        return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'Permission denied to edit the user #' .$id
            ];   
        return response($response, 401);          
        }

    }

    public function delete_user ($id) {
        //!!! Memo delete all tokens of this user
        User::destroy($id);
        if ($id > 1) {
        $response = [
            "success" => true,
            'message' => 'user #' .$id. ' deleted'
        ]; 
        return response($response, 201);
        } else {
            $response = [
                "success" => false,
                'message' => 'Permission denied to delete the user #' .$id
            ];   
        return response($response, 401);          
        }
    }

    public function login (Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
    
        // Check email
        $user = User::where('email', $fields['email'])->first();
        
        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                "success" => false,
                'message' => 'Your password is incorrect'
            ], 401);
        }


        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            "success" => true,
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        //auth()->user()->token()->delete();
        $request->user()->currentAccessToken()->delete();
        return [
            "success" => true,
            'message' => 'Logged out'
        ];
    }
}
