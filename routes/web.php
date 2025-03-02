<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [LoginController::class, 'showLoginPage'])->name('login');

Route::post('/login', [LoginController::class, 'login']);
