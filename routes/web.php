<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RfidController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::redirect('/', '/dashboard');

Route::get('/dashboard', [App\Http\Controllers\RfidController::class, 'index']);

Route::get('/user-management', [App\Http\Controllers\RfidController::class, 'userIndex'])->name('users.index');
Route::post('/user-management', [App\Http\Controllers\RfidController::class, 'userStore'])->name('users.store');
Route::get('/user-management/toggle/{id}', [App\Http\Controllers\RfidController::class, 'userToggle'])->name('users.toggle');
Route::delete('/user-management/{id}', [App\Http\Controllers\RfidController::class, 'userDelete'])->name('users.delete');
Route::get('/standby', [App\Http\Controllers\RfidController::class, 'standby'])->name('standby');
Route::post('/upload-webcam', [RfidController::class, 'uploadWebcam'])->name('upload.webcam');