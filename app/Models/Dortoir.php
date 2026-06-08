<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dortoir extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'sexe'
    ];
}
