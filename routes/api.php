<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;

Route::post('/registrar', [UserProfileController::class, 'registrar']);
Route::post('/login', [UserProfileController::class, 'acessar']);
Route::get('/usuarios', [UserProfileController::class, 'listagemUsuarios']);
