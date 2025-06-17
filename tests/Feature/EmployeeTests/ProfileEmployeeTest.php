<?php

use App\Http\Middleware\EnsureUserIsLoggedInMiddleware;
use App\Http\Middleware\IsUserHRMiddleware;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Leave\LeaveBalance;
use App\Models\Leave\LeaveType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');
    $this->withoutMiddleware([
        EnsureUserIsLoggedInMiddleware::class,
        IsUserHRMiddleware::class,
    ]);
    Employee::factory(5)->create();
    Mockery::close();
});

it('shows employee profile with all data', function (): void {
    // Create test data
    $employee = Employee::factory()->create([
        'employeeID' => 99,
    ]);
    $contracts = Contract::factory(2)->create(['employeeID' => $employee->employeeID]);
    LeaveType::factory()->count(5)->create();
    $balances = LeaveBalance::factory(3)->create([
        'employeeID' => $employee->employeeID,
        'year' => Carbon::now()->year,
    ]);

    $response = $this->get(route('hr.employee.profile', [
        'employeeID' => $employee->employeeID,
    ]));

    $response->assertOK()
        ->assertViewHas('employee', $employee)
        ->assertViewHas('contracts', function ($viewContracts) use ($contracts): bool {
            // Check if it's either a paginator with the expected items or the raw collection
            if ($viewContracts instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                return $viewContracts->total() === count($contracts) &&
                       $viewContracts->pluck('id')->toArray() === $contracts->pluck('id')->toArray();
            }

            return $viewContracts->pluck('id')->toArray() === $contracts->pluck('id')->toArray();
        })
        ->assertViewHas('balances', $balances);
});

it('handles missing employee', function (): void {
    $invalidId = 9999;

    $response = $this->get(route('hr.employee.profile', [
        'employeeID' => $invalidId,
    ]));

    $response->assertRedirect()
        ->assertSessionHasErrors([
            'employeeID' => 'Punonjësi me këtë ID nuk egziston.',
        ]);
});

it('shows empty contracts when none exist', function (): void {
    $employee = Employee::factory()->create();

    $response = $this->get(route('hr.employee.profile', [
        'employeeID' => $employee->employeeID,
    ]));

    $response->assertOk()
        ->assertViewHas('contracts', fn ($contracts): bool =>
            // Check if it's either a paginator with empty items or an empty collection
            ($contracts instanceof \Illuminate\Pagination\LengthAwarePaginator &&
                $contracts->isEmpty()) ||
               ($contracts instanceof \Illuminate\Support\Collection &&
                $contracts->isEmpty()));
});

it('handles database errors gracefully', function (): void {
    Log::shouldReceive('error')
        ->once()
        ->withArgs(fn ($message): bool => str_contains((string) $message, 'Error fetching employee data'));

    // Force a database error
    $this->mock(\App\Services\EmployeeService::class, function ($mock): void {
        $mock->shouldReceive('getEmployee')
            ->andThrow(new \Exception('Database error'));
    });

    $employee = Employee::factory()->create();

    $response = $this->get(route('hr.employee.profile', [
        'employeeID' => $employee->employeeID,
    ]));

    $response->assertRedirect()
        ->assertSessionHas('error', 'Ndodhi një gabim gjatë marrjes së të dhënave të profilit, provoni përsëri më vonë.');
});

it('validates employee ID parameter', function (): void {
    // Test invalid ID formats
    $response = $this->get(route('hr.employee.profile', [
        'employeeID' => 'invalid',
    ]));

    $response->assertSessionHasErrors(['employeeID']);

    // Test missing ID
    $response = $this->get(route('hr.employee.profile'));
    $response->assertSessionHasErrors(['employeeID']);
});
