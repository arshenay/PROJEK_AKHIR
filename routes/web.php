<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', [App\Http\Controllers\RfidController::class, 'index']);

Route::get('/user-management', [App\Http\Controllers\RfidController::class, 'userIndex'])->name('users.index');
Route::post('/user-management', [App\Http\Controllers\RfidController::class, 'userStore'])->name('users.store');
Route::get('/user-management/toggle/{id}', [App\Http\Controllers\RfidController::class, 'userToggle'])->name('users.toggle');
Route::delete('/user-management/{id}', [App\Http\Controllers\RfidController::class, 'userDelete'])->name('users.delete');