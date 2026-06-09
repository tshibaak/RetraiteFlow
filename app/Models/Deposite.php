<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposite extends Model
{
    protected $fillable = [
        'name',
        'sexe',
        'age',
        'group',
        'commission',
        'phone',
        'amount',
        'delai',
        'user_id',
        'validator_id',
        'status'
    ];
}
