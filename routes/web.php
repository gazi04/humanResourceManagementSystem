<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeRoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HumanResourceController;
use App\Http\Controllers\ManagerController;
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
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(EnsureUserIsNotLoggedInMiddleware::class)->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginPage'])->name('loginPage');
    Route::post('/authenticate', [LoginController::class, 'login'])->name('login');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserAdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'Admin.adminDashboard')->name('dashboard');

    Route::prefix('departaments')->name('department.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::post('/store', [DepartmentController::class, 'store'])->name('store');
        Route::delete('/destroy', [DepartmentController::class, 'destroy'])->name('destroy');
        Route::patch('/update', [DepartmentController::class, 'update'])->name('update');
        Route::get('/search', [DepartmentController::class, 'search'])->name('search');
    });

    Route::prefix('employees')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/administrators', [AdminController::class, 'index'])->name('administrators');
        Route::get('/managers', [ManagerController::class, 'index'])->name('managers');

        Route::post('/create', [EmployeeController::class, 'create'])->name('create');
        Route::delete('/delete', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::patch('/update', [EmployeeController::class, 'update'])->name('update');

        Route::patch('/assignRole', [EmployeeRoleController::class, 'update'])->name('assign-role');
        Route::get('/search', [EmployeeController::class, 'search'])->name('search');
    });
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserHRMiddleware::class])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', function () {
        return 'admin web page';
    })->name('dashboard');
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserManagerMiddleware::class])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/human-resources', [HumanResourceController::class, 'index'])->name('dashboard');
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
    $hr = Employee::create([
        'firstName' => 'gazi',
        'lastName' => 'gazi',
        'email' => 'gaz123221@gmail.com',
        'password' => Hash::make('123123'),
        'phone' => '045681371',
    ]);
    $manager = Employee::create([
        'firstName' => 'gazi',
        'lastName' => 'gazi',
        'email' => 'gazi32@gmail.com',
        'password' => Hash::make('123123'),
        'phone' => '045681370',
    ]);

    $role = Role::create(['roleName' => 'admin']);
    $hrRole = Role::create(['roleName' => 'hr']);
    $managerRole = Role::create(['roleName' => 'manager']);

    $employeeRole = EmployeeRole::create([
        'employeeID' => $admin['employeeID'],
        'roleID' => $role['roleID'],
    ]);

    EmployeeRole::create(
        [
            'employeeID' => $manager->employeeID,
            'roleID' => $managerRole->roleID,
        ],
        [
            'employeeID' => $hr->employeeID,
            'roleID' => $hrRole->roleID,
        ],
    );

    return redirect()->route('loginPage');
});

Route::get('/dummy-dep', function () {
    $department = Department::create([
        'departmentID' => '100',
        'departmentName' => 'testDepartment',
    ]);
});
