<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Middleware\EnsureUserIsLoggedIn;
use App\Http\Middleware\EnsureUserIsNotLoggedIn;
use App\Http\Middleware\IsUserAdmin;
use App\Http\Middleware\IsUserEmployee;
use App\Http\Middleware\IsUserHR;
use App\Http\Middleware\IsUserManager;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureUserIsLoggedIn::class])->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(EnsureUserIsNotLoggedIn::class)->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginPage'])->name('loginPage');
    Route::post('/authenticate', [LoginController::class, 'login'])->name('login');
});

Route::middleware([EnsureUserIsLoggedIn::class, IsUserAdmin::class])->group(function () {
    Route::get('/admin-dashboard', function () {
        return 'admin web page';
    })->name('admin-dashboard');

    Route::post('/create-department', [DepartmentController::class, 'create'])->name('create-department');
    Route::delete('/delete-department', [DepartmentController::class, 'delete'])->name('delete-department');
    Route::patch('/update-department', [DepartmentController::class, 'update'])->name('update-department');
});

Route::middleware([EnsureUserIsLoggedIn::class, IsUserHR::class])->group(function () {
    Route::get('/hr-dashboard', function () {
        return 'admin web page';
    })->name('hr-dashboard');
});

Route::middleware([EnsureUserIsLoggedIn::class, IsUserManager::class])->group(function () {
    Route::get('/manager-dashboard', function () {
        return 'admin web page';
    })->name('manager-dashboard');
});

Route::middleware([EnsureUserIsLoggedIn::class, IsUserEmployee::class])->group(function () {
    Route::get('/employee-dashboard', function () {
        return 'admin web page';
    })->name('employee-dashboard');
});

Route::get('/dummy-data', function () {
    $admin = Employee::create([
        'firstName' => 'gazi',
        'lastName' => 'gazi',
        'email' => 'gazi@gmail.com',
        'password' => Hash::make('gazigazi'),
        'phone' => '045681376',
    ]);

    $role = Role::create(['roleName' => 'admin']);
    $employeeRole = EmployeeRole::create([
        'employeeID' => $admin['employeeID'],
        'roleID' => $role['roleID'],
    ]);

    return redirect()->route('loginPage');
});

Route::get('/dummy-dep', function () {
    $department = Department::create([
        'departmentID' => '100',
        'departmentName' => 'testDepartment',
    ]);
});
