<?php

use App\Http\Controllers\AdminController;
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

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserAdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::prefix('departaments')->name('department.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::delete('/destroy', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::patch('/update', [DepartmentController::class, 'update'])->name('update');
    });

    Route::prefix('employees')->name('employee.')->group(function () {
        // Display list of all employees
        Route::get('/', [EmployeeController::class, 'employeers'])->name('employeers');

        // Display list of human resources employees
        Route::get('/human-resources', [EmployeeController::class, 'humanResources'])->name('human-resources');

        // Display list of administrators
        Route::get('/administrators', [EmployeeController::class, 'administrators'])->name('administrators');

        // Display list of managers
        Route::get('/managers', [EmployeeController::class, 'managers'])->name('managers');
    });
});

/* Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserAdminMiddleware::class])->group(function () { */
/*     Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin-dashboard'); */
/**/
/*     Route::post('/create-department', [DepartmentController::class, 'create'])->name('create-department'); */
/*     Route::delete('/delete-department', [DepartmentController::class, 'delete'])->name('delete-department'); */
/*     Route::patch('/update-department', [DepartmentController::class, 'update'])->name('update-department'); */
/* }); */

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserHRMiddleware::class])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', function () {
        return 'admin web page';
    })->name('dashboard');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserManagerMiddleware::class])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return 'admin web page';
    })->name('dashboard');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserEmployeeMiddleware::class])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', function () {
        return 'admin web page';
    })->name('dashboard');
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
