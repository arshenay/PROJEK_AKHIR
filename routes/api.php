<?php

use App\Http\Controllers\RfidController;
use Illuminate\Support\Facades\Route;

Route::post('/rfid-access', [RfidController::class, 'store'])
;

Route::get('/check-new-log', function() {
    return \App\Models\AccessLogin::latest()->first();
});