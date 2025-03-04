<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function() { return view('welcome'); })->name('dashboard');
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('loginPage');
Route::post('/authenticate', [LoginController::class, 'login'])->name('login');
Route::get('/signup', [LoginController::class, 'showSignupPage'])->name('signupPage');
Route::post('/register', [LoginController::class, 'register'])->name('register');
