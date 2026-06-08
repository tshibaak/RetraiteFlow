<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtelierModel extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'age'
    ];
}
