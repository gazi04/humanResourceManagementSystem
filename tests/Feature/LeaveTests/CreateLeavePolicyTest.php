<?php

use App\Http\Middleware\EnsureUserIsLoggedInMiddleware;
use App\Http\Middleware\IsUserHRMiddleware;
use App\Models\Leave\LeaveType;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');
    $this->roles = Role::get();
    $this->withoutMiddleware([
        EnsureUserIsLoggedInMiddleware::class,
        IsUserHRMiddleware::class,
    ]);
    $this->leaveType = LeaveType::factory()->create();
    $this->leavePolicy = $this->leaveType->policy;
    Mockery::close();
});

it('can store a new leave type with policies', function () {
    // Prepare the request data
    $data = [
        'name' => 'Annual Leave',
        'description' => 'Annual leave description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,

        // Policy data
        'annualQuota' => 20,
        'maxConsecutiveDays' => 10,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'restricedDays' => json_encode(['sunday', 'saturday']),
        'requirenments' => json_encode(['min_service_days' => 30]),

        // Roles
        'roles' => $this->roles->pluck('roleID')->toArray(),
    ];

    // Make the request
    $response = $this->post(route('hr.leave-type.store'), $data);

    // Assert the response
    $response->assertRedirect(route('hr.leave-type.index'))
        ->assertSessionHas('success');

    // Assert the leave type was created
    $leaveType = LeaveType::where('name', 'Annual Leave')->first();
    expect($leaveType)->not->toBeNull()
        ->description->toBe('Annual leave description')
        ->isPaid->toBe(1)
        ->requiresApproval->toBe(1)
        ->isActive->toBe(1);

    // Assert the policy was created
    expect($leaveType->policy)->not->toBeNull()
        ->annualQuota->toBe(20)
        ->maxConsecutiveDays->toBe(10)
        ->allowHalfDay->toBe(1)
        ->probationPeriodDays->toBe(90)
        ->carryOverLimit->toBe('5.00')
        ->restricedDays->toBe(json_encode(['sunday', 'saturday']))
        ->requirenments->toBe(json_encode(['min_service_days' => 30]));

    // Assert the roles were attached
    expect($leaveType->roles)->toHaveCount(4);
});

it('validates required fields when creating leave type with policies', function () {
    $response = $this->post(route('hr.leave-type.store'), []);

    $response->assertSessionHasErrors([
        'name',
        'isPaid',
        'requiresApproval',
        'isActive',
        'annualQuota',
        'allowHalfDay',
        'probationPeriodDays',
        'carryOverLimit',
        'roles',
    ]);
});

it('validates name uniqueness when creating leave type', function () {
    $existingLeaveType = LeaveType::factory()->create(['name' => 'Existing Leave']);

    $data = [
        'name' => 'Existing Leave', // Duplicate name
        'description' => 'Description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,
        'annualQuota' => 20,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'roles' => $this->roles->pluck('roleID')->toArray(),
    ];

    $response = $this->post(route('hr.leave-type.store'), $data);

    $response->assertSessionHasErrors(['name' => 'Ekziston tashmë një lloj leje me këtë emër.']);
});

it('validates numeric fields for policy data', function () {
    $data = [
        'name' => 'Invalid Policy Leave',
        'description' => 'Description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,
        'annualQuota' => 'not-a-number',
        'maxConsecutiveDays' => 'not-a-number',
        'allowHalfDay' => true,
        'probationPeriodDays' => 'not-a-number',
        'carryOverLimit' => 'not-a-number',
        'roles' => $this->roles->pluck('roleID')->toArray(),
    ];

    $response = $this->post(route('hr.leave-type.store'), $data);

    $response->assertSessionHasErrors([
        'annualQuota',
        'maxConsecutiveDays',
        'probationPeriodDays',
        'carryOverLimit',
    ]);
});

it('validates minimum values for policy numeric fields', function () {
    $data = [
        'name' => 'Negative Policy Leave',
        'description' => 'Description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,
        'annualQuota' => -1,
        'maxConsecutiveDays' => 0, // min is 1 when present
        'allowHalfDay' => true,
        'probationPeriodDays' => -1,
        'carryOverLimit' => -1,
        'roles' => $this->roles->pluck('roleID')->toArray(),
    ];

    $response = $this->post(route('hr.leave-type.store'), $data);

    $response->assertSessionHasErrors([
        'annualQuota',
        'maxConsecutiveDays',
        'probationPeriodDays',
        'carryOverLimit',
    ]);
});

it('validates json fields for policy data', function () {
    $data = [
        'name' => 'Invalid JSON Leave',
        'description' => 'Description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,
        'annualQuota' => 20,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'restricedDays' => 'not-valid-json',
        'requirenments' => 'not-valid-json',
        'roles' => $this->roles->pluck('roleID')->toArray(),
    ];

    $response = $this->post(route('hr.leave-type.store'), $data);

    $response->assertSessionHasErrors([
        'restricedDays',
        'requirenments',
    ]);
});

it('validates role existence when creating leave type', function () {
    $invalidRoleId = 9999; // Assuming this doesn't exist

    $data = [
        'name' => 'Invalid Role Leave',
        'description' => 'Description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,
        'annualQuota' => 20,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'roles' => [$invalidRoleId],
    ];

    $response = $this->post(route('hr.leave-type.store'), $data);
    $response->assertSessionHasErrors(['roles.*']);
    $errors = session('errors');
    expect($errors->first('roles.*'))->toContain('role të zgjedhura nuk janë të vlefshme');
});

it('rolls back all changes if an error occurs during creation', function () {
    // Mock the service to throw an exception
    $this->mock(\App\Services\LeaveService::class, function ($mock) {
        $mock->shouldReceive('createLeaveTypeWithPolicy')
            ->andThrow(new \RuntimeException('Something went wrong'));
    });

    $data = [
        'name' => 'Failing Leave',
        'description' => 'Description',
        'isPaid' => true,
        'requiresApproval' => true,
        'isActive' => true,
        'annualQuota' => 20,
        'allowHalfDay' => true,
        'probationPeriodDays' => 90,
        'carryOverLimit' => 5,
        'roles' => $this->roles->pluck('roleID')->toArray(),
    ];

    $leaveTypeCountBefore = LeaveType::count();
    $leavePolicyCountBefore = DB::table('leave_policies')->count();
    $pivotCountBefore = DB::table('leave_type_role')->count();

    $response = $this->post(route('hr.leave-type.store'), $data);

    $response->assertRedirect(route('hr.leave-type.edit'))
        ->assertSessionHas('error', 'Something went wrong');

    expect(LeaveType::count())->toBe($leaveTypeCountBefore);
    expect(DB::table('leave_policies')->count())->toBe($leavePolicyCountBefore);
    expect(DB::table('leave_type_role')->count())->toBe($pivotCountBefore);
});
