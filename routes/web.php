<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeRoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HumanResourceController;
use App\Http\Controllers\Leave\LeaveBalanceController;
use App\Http\Controllers\Leave\LeaveTypeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\EnsureUserIsLoggedInMiddleware;
use App\Http\Middleware\EnsureUserIsNotLoggedInMiddleware;
use App\Http\Middleware\IsUserAdminMiddleware;
use App\Http\Middleware\IsUserEmployeeMiddleware;
use App\Http\Middleware\IsUserHRMiddleware;
use App\Http\Middleware\IsUserManagerMiddleware;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/get/hrs', [HumanResourceController::class, 'getHrs']);
Route::view('/test/api/view', 'testapi');

/* TODO- NEED TO TEST THE MIDDLEWARE */
Route::middleware([EnsureUserIsLoggedInMiddleware::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
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
        Route::get('/search/manager', [DepartmentController::class, 'searchManager'])->name('search.manager');
    });

    Route::prefix('employees')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/administrators', [AdminController::class, 'index'])->name('administrators');
        Route::get('/managers', [ManagerController::class, 'index'])->name('managers');
        /* TODO- DIMAL QIT ROUTE POSHT PERDORE SI API EDHE BONI FETCH KSHYRE A PO MUNDESH ME NDREQ */
        Route::get('/hrs', [HumanResourceController::class, 'getHrs'])->name('hrs');

        Route::post('/create', [EmployeeController::class, 'create'])->name('create');
        Route::delete('/delete', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::patch('/update', [EmployeeController::class, 'update'])->name('update');

        Route::patch('/assignRole', [EmployeeRoleController::class, 'update'])->name('assign-role');
        Route::get('/search', [EmployeeController::class, 'search'])->name('search');
    });

    Route::prefix('tickets')->name('ticket.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/show', [TicketController::class, 'show'])->name('show');
        Route::post('/finish', [TicketController::class, 'finish'])->name('finish');
    });
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserHRMiddleware::class])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', function () {
        return view('test');
    })->name('dashboard');
    Route::get('/', [HumanResourceController::class, 'index'])->name('dashboard');

    Route::prefix('employees')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');

        Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
        Route::get('/search', [EmployeeController::class, 'search'])->name('search');

        Route::prefix('contracts')->name('contract.')->group(function () {
            Route::post('/getContracts', [ContractController::class, 'index'])->name('show');
            Route::post('/upload', [ContractController::class, 'create'])->name('upload');
            Route::post('/download', [ContractController::class, 'show'])->name('download');
            Route::patch('/update', [ContractController::class, 'update'])->name('update');
            Route::delete('/delete', [ContractController::class, 'delete'])->name('delete');
        });
    });

    Route::prefix('tickets')->name('ticket.')->group(function () {
        Route::get('/', function () {
            return view('Hr.createTicket');
        })->name('index');
        Route::post('/create', [TicketController::class, 'create'])->name('create');
    });

    Route::prefix('leave-types')->name('leave-type.')->controller(LeaveTypeController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/update', 'update')->name('update');
        Route::get('/is-active', 'toggleIsActive')->name('is-active');
    });

    Route::prefix('leave-balances')->name('leave-balance')->controller(LeaveBalanceController::class)->group(function () {
        Route::get('/initialize-yearly-balance', 'initYearlyBalance');
        Route::patch('/add-days', 'addDaysFromBalance');
        Route::patch('/deduct-days', 'deductDaysFromBalance');
    });
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserManagerMiddleware::class])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/', [HumanResourceController::class, 'index'])->name('dashboard');

    Route::prefix('tickets')->name('ticket.')->group(function () {
        Route::get('/', function () {
            return view('Manager.createTicket');
        })->name('index');
        Route::post('/create', [TicketController::class, 'create'])->name('create');
    });
});

Route::middleware([EnsureUserIsLoggedInMiddleware::class, IsUserEmployeeMiddleware::class])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', function () {
        return 'admin web page';
    })->name('dashboard');

    Route::prefix('tickets')->name('ticket.')->group(function () {
        Route::get('/', function () {
            return view('Employee.createTicket');
        })->name('index');
        Route::post('/create', [TicketController::class, 'create'])->name('create');
    });
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

    $employeeRole = EmployeeRole::create([
        'employeeID' => $admin['employeeID'],
        'roleID' => 1,
    ]);

    EmployeeRole::create([
        'employeeID' => $manager->employeeID,
        'roleID' => 4,
    ]);
    EmployeeRole::create([
        'employeeID' => $hr->employeeID,
        'roleID' => 2,
    ]);

    return redirect()->route('loginPage');
});

Route::get('/dummy-dep', function () {
    $department = Department::create([
        'departmentID' => '100',
        'departmentName' => 'testDepartment',
    ]);
});
