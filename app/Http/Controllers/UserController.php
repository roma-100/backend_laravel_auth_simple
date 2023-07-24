<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function list() {
 
        $users = User::orderBy('id', 'asc')->get();
        
        return $users;
    }

    public function list_active_users() {
 /* where('mk_list_id', $mk_list_id) */
        $users = User::select('id', 'name', 'position')
        ->where('active', true)
        ->where('role', 'user')
        ->orderBy('id', 'asc')
        ->get();
        
        return $users;
    }
}
