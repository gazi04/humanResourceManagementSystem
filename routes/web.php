<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\EnsureUserIsLoggedIn;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

Route::get('/', function() {return Auth::guard('employee')->user();})->name('dashboard')->middleware(EnsureUserIsLoggedIn::class);
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('loginPage');
Route::post('/authenticate', [LoginController::class, 'login'])->name('login');
