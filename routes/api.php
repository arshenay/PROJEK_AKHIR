<?php

use App\Http\Controllers\RfidController;
use Illuminate\Support\Facades\Route;

// Tidak perlu pakai /api lagi di sini, karena sudah otomatis
Route::post('/rfid-access', [RfidController::class, 'store']);