<?php

use App\Models\Employee;
use App\Models\Leave\LeaveRequest;
use App\Models\Leave\LeaveType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');

    $this->employee = Employee::factory()->withRole('employee')->create();

    $this->hr = Employee::factory()->withRole('hr')->create();

    $this->leaveType = LeaveType::create([
        'name' => 'Annual Leave',
        'isActive' => true,
    ]);
    // Create leave balance for the employee
    \App\Models\Leave\LeaveBalance::factory()
        ->forEmployee($this->employee)
        ->forLeaveType($this->leaveType)
        ->forYear(now()->year)
        ->create([
            'remainingDays' => 20, // Ensure enough days for testing
            'usedDays' => 0,
        ]);
    Auth::guard('employee')->login($this->hr);
});

test('employee can access leave request page', function (): void {
    Auth::guard('employee')->login($this->employee);
    $response = $this->get('/leave-request');

    $response->assertStatus(200);
});

test('employee can submit valid leave request', function (): void {
    Auth::guard('employee')->login($this->employee);

    $file = UploadedFile::fake()->create('document.pdf', 1000);

    $leaveData = [
        'employeeID' => $this->employee->employeeID,
        'leaveTypeID' => $this->leaveType->leaveTypeID,
        'startDate' => now()->addDay()->format('Y-m-d'),
        'endDate' => now()->addDays(3)->format('Y-m-d'),
        'durationType' => 'multiDay',
        'requestedDays' => 3,
        'reason' => 'Family vacation',
        'attachments' => [$file],
    ];

    $response = $this->post('/leave-request/store', $leaveData);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('leave_requests', [
        'employeeID' => $this->employee->employeeID,
        'leaveTypeID' => $this->leaveType->leaveTypeID,
        'reason' => 'Family vacation',
        'status' => 'pending',
    ]);
});

test('employee cannot submit invalid leave request', function (array $invalidData, array $expectedErrors): void {
    Auth::guard('employee')->login($this->employee);

    $response = $this->post(route('leave-request.store'), $invalidData);

    $response->assertSessionHasErrors($expectedErrors);
})->with([
    'invalid dates' => [
        [
            'employeeID' => 1,
            'leaveTypeID' => 1,
            'startDate' => now()->subDay()->format('Y-m-d'),
            'endDate' => now()->subDays(3)->format('Y-m-d'),
            'durationType' => 'multiDay',
            'requestedDays' => 3,
            'reason' => 'Family vacation',
        ],
        ['startDate', 'endDate'],
    ],
    'invalid half day request' => [
        [
            'employeeID' => 1,
            'leaveTypeID' => 1,
            'startDate' => now()->addDay()->format('Y-m-d'),
            'endDate' => now()->addDay()->format('Y-m-d'),
            'durationType' => 'halfDay',
            'requestedDays' => 0.5,
            'reason' => 'Family vacation',
        ],
        ['halfDayType'],
    ],
]);

test('hr can view leave requests', function (): void {
    $response = $this->get(route('hr.leave-request.index'));

    $response->assertOK();
});

test('hr can approve leave request', function (): void {
    Auth::guard('employee')->login($this->hr);

    $year = now()->year;

    $leaveRequest = LeaveRequest::factory()
        ->pending()
        ->create([
            'employeeID' => $this->employee->employeeID,
            'leaveTypeID' => $this->leaveType->leaveTypeID,
            'startDate' => now()->setYear($year),
            'endDate' => now()->setYear($year)->addDays(4),
            'requestedDays' => 5,
            'durationType' => 'multiDay',
        ]);

    $balanceCheck = \App\Models\Leave\LeaveBalance::where([
        'employeeID' => $this->employee->employeeID,
        'leaveTypeID' => $this->leaveType->leaveTypeID,
        'year' => $year,
    ])->first();
    \Log::info('LeaveBalance check:', [
        'balance' => $balanceCheck ? $balanceCheck->toArray() : null,
    ]);

    \Illuminate\Support\Facades\Cache::flush();

    $response = $this->patch(route('hr.leave-request.approve'), [
        'leaveRequestID' => $leaveRequest->leaveRequestID,
    ]);

    $response->assertRedirect(route('hr.leave-request.index'));
    $response->assertSessionHas('success', 'Kërkesa e pushimit u miratua me sukses.');
    $response->assertSessionMissing('error');

    $this->assertDatabaseHas('leave_requests', [
        'leaveRequestID' => $leaveRequest->leaveRequestID,
        'status' => 'approved',
    ]);

    $this->assertDatabaseHas('leave_balances', [
        'employeeID' => $this->employee->employeeID,
        'leaveTypeID' => $this->leaveType->leaveTypeID,
        'year' => $year,
        'remainingDays' => 20 - $leaveRequest->requestedDays,
        'usedDays' => $leaveRequest->requestedDays,
    ]);
});

test('hr can reject leave request', function (): void {
    $leaveRequest = LeaveRequest::factory()
        ->pending()
        ->create([
            'employeeID' => $this->employee->employeeID,
            'leaveTypeID' => $this->leaveType->leaveTypeID,
        ]);

    $response = $this->patch(route('hr.leave-request.reject'), [
        'leaveRequestID' => $leaveRequest->leaveRequestID,
        'reason' => 'Nuk ka punetore te mjaftueshëm',
    ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('leave_requests', [
        'leaveRequestID' => $leaveRequest->leaveRequestID,
        'rejectionReason' => 'Nuk ka punetore te mjaftueshëm',
        'status' => 'rejected',
    ]);
});

test('unauthenticated user cannot access leave request page', function (): void {
    Auth::guard('employee')->logout($this->employee);
    $response = $this->get('/leave-request');
    $response->assertRedirect('/login');
});

test('regular employee cannot access hr leave request management', function (): void {
    Auth::guard('employee')->login($this->employee);

    $response = $this->get(route('hr.leave-request.index'));

    $response->assertStatus(403);
});
