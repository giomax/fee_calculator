<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index']);
Route::post('/uploadFile', [HomeController::class, 'uploadFile']);