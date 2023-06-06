<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MkList extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'name',
        'decsription',
        'quantity',
        'date_start',
        'date_finish',
        'active'
    ];
}
/* name decsription quantity date_start date_finish active */