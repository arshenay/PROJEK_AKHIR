<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLogin extends Model
{
    protected $table = 'access_logins';
    protected $fillable = [
        'rfid_uid', 
        'status',
        'image'
        ];
}
