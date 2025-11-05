<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users/create', [UserController::class, 'store']); // Kullanıcı oluşturma
Route::get('/users/list', [UserController::class, 'index']); // Kullanıcı Listeleme
Route::put('/users/update/{id}', [UserController::class, 'update']); // Kullanıcı güncelleme
Route::delete('/users/delete/{id}', [UserController::class, 'destroy']); // Kullanıcı silme
