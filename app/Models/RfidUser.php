<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidUser extends Model
{
    protected $fillable = 
    ['name',
    'rfid_uid',
    'is_active
    '];
}
