<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MkStep extends Model
{
    use HasFactory;
    protected $fillable = [
        'mk_list_id',
        'step_num',
        'action',
        'description',
        'duration',
    ];
}

/* id mk_list_id  action description duration */