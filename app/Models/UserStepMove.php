<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStepMove extends Model
{
    use HasFactory;
    protected $fillable = [
        'mk_list_id',
        'user_id',
        'items',
        'step_tx',
        'step_rx',
    ];
}
