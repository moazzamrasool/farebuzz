<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuery extends Model
{
    use HasFactory;

    function state(){
        return $this->belongsTo(State::class, 'state');
    }
    function city(){
        return $this->belongsTo(City::class, 'destination');
    }
}
