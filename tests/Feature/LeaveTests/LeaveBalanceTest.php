<?php

use App\Models\Employee;
use App\Models\Leave\LeaveBalance;
use App\Models\Leave\LeaveType;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');
    $this->withoutMiddleware([
        \App\Http\Middleware\EnsureUserIsLoggedInMiddleware::class,
        \App\Http\Middleware\IsUserHRMiddleware::class,
    ]);

    // Create test data
    Employee::factory(5)
        ->withRole()
        ->create();
    LeaveType::factory()->create(['isActive' => true]);
    Mockery::close();
});

it('loads the balances in the view', function (): void {
    $employee = Employee::factory()
        ->withRole()
        ->create(['employeeID' => 99]);
    $balances = LeaveBalance::factory(3)->create([
        'employeeID' => $employee->employeeID,
        'year' => now()->year,
    ]);

    $response = $this->get(route('hr.employee.profile', [
        'employeeID' => $employee->employeeID,
    ]));

    $response->assertOk()
        ->assertViewHas('balances', $balances);
});

it('initializes yearly balances successfully', function (): void {
    $hr = Employee::factory()->hr()->create();
    Auth::guard('employee')->login($hr);

    /** @var Employee $employee */
    $employee = Employee::factory()
        ->withRole()
        ->create(['status' => 'Active']);
    $leaveType = LeaveType::factory()->create(['isActive' => true]);

    $response = $this->get(route('hr.leave-balance.init'));

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHas('success', 'Bilancet e pushimeve u gjeneruan për punonjësit me sukses.');

    expect(LeaveBalance::where('employeeID', $employee->employeeID)
        ->where('year', now()->year)
        ->exists())->toBeTrue();
});

it('handles already initialized year', function (): void {
    $this->mock(\App\Services\LeaveService::class)
        ->shouldReceive('initializeYearlyBalances')
        ->andThrow(new \RuntimeException('Bilancet e pushimeve për vitin 2023 janë inicializuar tashmë.'));

    $response = $this->get(route('hr.leave-balance.init'));

    $response->assertRedirect()
        ->assertSessionHas('error', 'Bilancet e pushimeve për vitin 2023 janë inicializuar tashmë.');
});

it('adds days to balance successfully', function (): void {
    $employee = Employee::factory()
        ->withRole()
        ->create();
    $leaveBalance = LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'remainingDays' => 10,
        'usedDays' => 5,
    ]);

    $response = $this->patch(route('hr.leave-balance.add'), [
        'leaveBalanceID' => $leaveBalance->leaveBalanceID,
        'days' => 2,
    ]);

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHas('success', 'Bilanci i pushimeve u përditësua me sukses.');

    $updatedBalance = LeaveBalance::find($leaveBalance->leaveBalanceID);
    expect($updatedBalance->remainingDays)->toBe('12.00')
        ->and($updatedBalance->usedDays)->toBe('3.00');
});

it('validates add days request', function (): void {
    // Invalid leaveBalanceID
    $response = $this->patch(route('hr.leave-balance.add'), [
        'leaveBalanceID' => 999,
        'days' => 2,
    ]);

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHasErrors(['leaveBalanceID' => 'Bilanci i lejes me këtë ID nuk egziston.']);

    // Invalid days
    $leaveBalance = LeaveBalance::factory()->create();

    $response = $this->patch(route('hr.leave-balance.add'), [
        'leaveBalanceID' => $leaveBalance->leaveBalanceID,
        'days' => -1,
    ]);

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHasErrors(['days' => 'Numri i ditëve duhet të jetë më i madh se 0.']);
});

it('deducts days from balance successfully', function (): void {
    $employee = Employee::factory()
        ->withRole()
        ->create();
    $leaveBalance = LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'remainingDays' => 10,
        'usedDays' => 0,
    ]);

    $response = $this->patch(route('hr.leave-balance.deduct'), [
        'leaveBalanceID' => $leaveBalance->leaveBalanceID,
        'days' => 3,
    ]);

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHas('success', 'Bilanci i pushimeve u përditësua me sukses.');

    $updatedBalance = LeaveBalance::find($leaveBalance->leaveBalanceID);
    expect($updatedBalance->remainingDays)->toBe('7.00')
        ->and($updatedBalance->usedDays)->toBe('3.00');
});

it('prevents deducting more days than available', function (): void {
    $employee = Employee::factory()
        ->withRole()
        ->create();
    $leaveBalance = LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'remainingDays' => 5,
        'usedDays' => 0,
    ]);

    $this->mock(\App\Services\LeaveService::class)
        ->shouldReceive('deductDays')
        ->andThrow(new \RuntimeException('Bilanc i pamjaftueshëm pushimesh. Në dispozicion: 5, Kërkuar: 10'));

    $response = $this->patch(route('hr.leave-balance.deduct'), [
        'leaveBalanceID' => $leaveBalance->leaveBalanceID,
        'days' => 10,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('error', 'Bilanc i pamjaftueshëm pushimesh. Në dispozicion: 5, Kërkuar: 10');
});

it('validates deduct days request', function (): void {
    // Invalid leaveBalanceID
    $response = $this->patch(route('hr.leave-balance.deduct'), [
        'leaveBalanceID' => 999,
        'days' => 2,
    ]);

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHasErrors(['leaveBalanceID' => 'Bilanci i lejes me këtë ID nuk egziston.']);

    // Invalid days
    $leaveBalance = LeaveBalance::factory()->create();

    $response = $this->patch(route('hr.leave-balance.deduct'), [
        'leaveBalanceID' => $leaveBalance->leaveBalanceID,
        'days' => -1,
    ]);

    $response->assertRedirect(route('hr.dashboard'))
        ->assertSessionHasErrors(['days' => 'Numri i ditëve duhet të jetë më i madh se 0.']);
});
