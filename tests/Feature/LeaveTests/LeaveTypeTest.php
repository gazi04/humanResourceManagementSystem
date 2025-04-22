<?php

use App\Http\Middleware\EnsureUserIsLoggedInMiddleware;
use App\Http\Middleware\IsUserHRMiddleware;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Leave\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');
    $this->withoutMiddleware([
        EnsureUserIsLoggedInMiddleware::class,
        IsUserHRMiddleware::class
    ]);
    Mockery::close();
});

it('successfully creates a leave type with valid data', function () {
    $response = $this->post(route('hr.leave-type.store'), [
        // Leave type data
        'name' => 'Annual Leave',
        'description' => 'Paid annual leave',
        'isPaid' => true,
        'requiresApproval' => false,
        'isActive' => true,

        // Leave policy data
        'annualQuota' => 20,
        'maxConsecutiveDays' => 15,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'restricedDays' => json_encode(['Monday', 'Friday']),
        'requirenments' => json_encode(['minServiceDays' => 180]),

        // Roles
        'roles' => [1, 2, 3, 4],
    ]);

    $response->assertRedirectToRoute('hr.leave-type.index')
        ->assertSessionHas('success', 'Lloji i pushimit u krijua me sukses.');

    // Assert leave type was created
    $this->assertDatabaseHas('leave_types', [
        'name' => 'Annual Leave',
        'isPaid' => true,
        'isActive' => true,
    ]);

    // Assert leave policy was created
    $this->assertDatabaseHas('leave_policies', [
        'annualQuota' => 20,
        'allowHalfDay' => true,
    ]);

    // Assert roles were attached
    $leaveType = LeaveType::first();
    $this->assertCount(4, $leaveType->roles);
});

it('validates required fields', function () {
    $response = $this->post(route('hr.leave-type.store'), []);

    $response->assertInvalid([
        'name' => 'Emri i llojit të lejes është i detyrueshëm.',
        'isPaid' => 'Statusi i pagesës është i detyrueshëm.',
        'annualQuota' => 'Kuota vjetore është e detyrueshme.',
        'roles' => 'Ju lutemi, zgjidhni të paktën një rol.',
    ]);
});

it('rejects duplicate leave type names', function () {
    LeaveType::create(['name' => 'Existing Leave']);

    $response = $this->post(route('hr.leave-type.store'), [
        'name' => 'Existing Leave',
        // Other required fields...
    ]);

    $response->assertInvalid([
        'name' => 'Ekziston tashmë një lloj leje me këtë emër.',
    ]);
});

/* TODO- IN THE TEST BELOW THE DB MOCK ISN'T WORKING AS I EXPECTED */
/* it('handles database errors gracefully', function () { */
/*     $this->mock(\App\Services\LeaveService::class, function ($mock) { */
/*         $mock->shouldReceive('createLeaveTypeWithPolicy') */
/*             ->andThrow(new \RuntimeException('Database error')); */
/*     }); */
/**/
/*     DB::partialMock() */
/*         ->shouldReceive('transaction') */
/*         ->andThrow(new \Exception('Database error')); */
/**/
/*     $response = $this->post(route('hr.leave-type.store'), [ */
/*         // Leave type data */
/*         'name' => 'Annual Leave', */
/*         'description' => 'Paid annual leave', */
/*         'isPaid' => true, */
/*         'requiresApproval' => false, */
/*         'isActive' => true, */
/**/
/*         // Leave policy data */
/*         'annualQuota' => 20, */
/*         'maxConsecutiveDays' => 15, */
/*         'allowHalfDay' => true, */
/*         'probationPeriodDays' => 90, */
/*         'carryOverLimit' => 5, */
/*         'restricedDays' => json_encode(['Monday', 'Friday']), */
/*         'requirenments' => json_encode(['minServiceDays' => 180]), */
/**/
/*         // Roles */
/*         'roles' => [1, 2, 3, 4], */
/*     ]); */
/**/
/*     $response->assertRedirectToRoute('hr.leave-type.edit') */
/*         ->assertSessionHas('error', 'Database error'); */
/* }); */

it('requires HR role to create leave types', function () {
    $this->withMiddleware([
        IsUserHRMiddleware::class,
    ]);

    // Create and authenticate a non-HR employee
    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'test@example.com',
        'phone' => '987954321',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $employee->employeeID,
        'roleID' => 3,
    ]);

    Auth::guard('employee')->login($employee);

    $response = $this->post(route('hr.leave-type.store'), [
        // Leave type data
        'name' => 'Annual Leave',
        'description' => 'Paid annual leave',
        'isPaid' => true,
        'requiresApproval' => false,
        'isActive' => true,

        // Leave policy data
        'annualQuota' => 20,
        'maxConsecutiveDays' => 15,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'restricedDays' => json_encode(['Monday', 'Friday']),
        'requirenments' => json_encode(['minServiceDays' => 180]),

        // Roles
        'roles' => [1, 2, 3, 4],
    ]);

    $response->assertForbidden();
});

it('validates json fields', function () {
    $response = $this->post(route('hr.leave-type.store'), [
        'restricedDays' => 'not-json',
        'requirenments' => 'not-json',
    ]);

    $response->assertInvalid([
        'restricedDays' => 'Ditët e kufizuara duhet të jenë në format JSON.',
        'requirenments' => 'Kërkesat duhet të jenë në format JSON.',
    ]);
});

it('validates numeric fields', function () {
    $response = $this->post(route('hr.leave-type.store'), [
        'annualQuota' => 'not-a-number',
        'carryOverLimit' => 'not-a-number',
    ]);

    $response->assertInvalid([
        'annualQuota' => 'Kuota vjetore duhet të jetë një numër i plotë.',
        'carryOverLimit' => 'Limiti i bartjes duhet të jetë një numër.',
    ]);
});
