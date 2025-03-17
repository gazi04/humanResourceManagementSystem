<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Middleware\EnsureUserIsLoggedInMiddleware;
use App\Http\Middleware\EnsureUserIsNotLoggedInMiddleware;
use App\Http\Middleware\IsUserAdminMiddleware;
use App\Http\Middleware\IsUserEmployeeMiddleware;
use App\Http\Middleware\IsUserHRMiddleware;
use App\Http\Middleware\IsUserManagerMiddleware;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::middleware([EnsureUserIsLoggedInMiddleware::class])->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(EnsureUserIsNotLoggedInMiddleware::class)->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginPage'])->name('loginPage');
    Route::post('/authenticate', [LoginController::class, 'login'])->name('login');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserAdminMiddleware::class])->group(function () {
    Route::get('/admin-dashboard', function () {
        return 'admin web page';
    })->name('admin-dashboard');

    Route::post('/create-department', [DepartmentController::class, 'create'])->name('create-department');
    Route::delete('/delete-department', [DepartmentController::class, 'delete'])->name('delete-department');
    Route::patch('/update-department', [DepartmentController::class, 'update'])->name('update-department');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserHRMiddleware::class])->group(function () {
    Route::get('/hr-dashboard', function () {
        return 'admin web page';
    })->name('hr-dashboard');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserManagerMiddleware::class])->group(function () {
    Route::get('/manager-dashboard', function () {
        return 'admin web page';
    })->name('manager-dashboard');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserEmployeeMiddleware::class])->group(function () {
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
