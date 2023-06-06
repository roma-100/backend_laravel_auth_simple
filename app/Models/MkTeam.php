<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MkTeam extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'mk_list_id',
        'role',
        'items',
    ];
}
