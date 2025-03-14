<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Middleware\EnsureUserIsLoggedIn;
use App\Http\Middleware\IsUserHR;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

Route::get('/', [EmployeeController::class, 'index'])->name('dashboard')->middleware([EnsureUserIsLoggedIn::class]);
Route::get('/login', [LoginController::class, 'showLoginPage'])->name('loginPage');
Route::post('/authenticate', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/dummy-data', function() {
    $admin = Employee::create([
        'firstName' => 'gazi',
        'lastName' => 'gazi',
        'email' => 'gazi@gmail.com',
        'password' => Hash::make('gazigazi'),
        'phone' => '045681376'
    ]);

    $role = Role::create(['roleName' => 'admin']);
    $employeeRole = EmployeeRole::create([
        'employeeID' => $admin['employeeID'],
        'roleID' => $role['roleID']
    ]);

    return redirect()->route('loginPage');
});
