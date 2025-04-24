<?php

use App\Http\Middleware\EnsureUserIsLoggedInMiddleware;
use App\Http\Middleware\IsUserHRMiddleware;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Leave\LeaveType;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');
    $this->withoutMiddleware([
        EnsureUserIsLoggedInMiddleware::class,
        IsUserHRMiddleware::class,
    ]);
    Mockery::close();
});

/*
 * INDEX LEAVE TYPE PAGE
 */
it('displays leave types in index view', function () {
    $response = $this->get(route('hr.leave-type.index'));

    $response->assertViewIs('Hr.LeaveType.index');
    $response->assertViewHas('leaveTypes');
    /* TODO- NEED TO ASSERT IF THE LEAVE TYPES ARE IN THE VIEW AFTER THE FRONT-END PART IS DONE */
});

it('shows error message when leave types cannot be fetched', function () {
    $this->mock(\App\Services\LeaveService::class, function ($mock) {
        $mock->shouldReceive('getLeaveTypes')
            ->andThrow(new \RuntimeException('Database error'));
    });

    $response = $this->get(route('hr.leave-type.index'));

    $response->assertViewIs('Hr.LeaveType.index')
        ->assertViewHas('error', 'Database error');
});

it('successfully retrieves paginated leave types', function () {
    LeaveType::factory()->count(1)->create();
    $result = app(\App\Services\LeaveService::class)->getLeaveTypes();

    expect($result)
        ->toBeInstanceOf(LengthAwarePaginator::class)
        ->toHaveCount(1);
});

it('includes leave policy data in results', function () {
    LeaveType::factory()->count(5)->create();
    $result = app(\App\Services\LeaveService::class)->getLeaveTypes();

    expect($result->first())
        ->toHaveKeys([
            'leaveTypeID',
            'name',
            'description',
            'isPaid',
            'requiresApproval',
            'isActive',
            'leavePolicyID',
        ]);
});

it('handles exceptions for getLeaveTypes', function () {
    DB::shouldReceive('transaction')
        ->andThrow(new \Exception('Bla bla bla'));

    expect(fn () => app(\App\Services\LeaveService::class)->getLeaveTypes())
        ->toThrow(\RuntimeException::class, 'Ndodhi një gabim i papritur në marrjen e llojeve të pushimeve.');
});

it('handles PDO exception for getLeaveTypes', function () {
    DB::shouldReceive('transaction')
        ->andThrow(new \PDOException('PDO exception!!'));

    expect(fn () => app(\App\Services\LeaveService::class)->getLeaveTypes())
        ->toThrow(\RuntimeException::class, 'Lidhja me bazën e të dhënave dështoi.');
});

it('handles query exception for getLeaveTypes', function () {
    $query = 'select `lt`.`leaveTypeID`, `lt`.`name`, `lt`.`description`, '.
             '`lt`.`isPaid`, `lt`.`requiresApproval`, `lt`.`isActive`, '.
             '`lp`.`leavePolicyID` from `leave_types` as `lt` left join '.
             '`leave_policies` as `lp` on `lt`.`leaveTypeID` = `lp`.`leaveTypeID` '.
             'order by `lt`.`created_at` desc';

    $pdoException = new PDOException('PDO test exception: invalid select statement');

    $queryException = new QueryException(
        env('DB_CONNECTION'),
        $query,
        [],
        $pdoException
    );

    DB::shouldReceive('transaction')
        ->andThrow($queryException);

    // Verify logging
    Log::shouldReceive('error')
        ->once()
        ->with('Database error in getLeaveTypes: '.$queryException->getMessage());

    expect(fn () => app(\App\Services\LeaveService::class)->getLeaveTypes())
        ->toThrow(\RuntimeException::class, 'Marrja e llojeve të pushimeve dështoi për shkak të një gabimi në bazën e të dhënave.');
});

/*
 * CREATE LEAVE TYPE
 */
it('opens the create leave type view', function () {
    $response = $this->get(route('hr.leave-type.create'));
    $response->assertOK()
        ->assertViewIs('Hr.LeaveType.create');
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

it('validates required fields to create a new leave type', function () {
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

it('validates json fields to create a leave type', function () {
    $response = $this->post(route('hr.leave-type.store'), [
        'restricedDays' => 'not-json',
        'requirenments' => 'not-json',
    ]);

    $response->assertInvalid([
        'restricedDays' => 'Ditët e kufizuara duhet të jenë në format JSON.',
        'requirenments' => 'Kërkesat duhet të jenë në format JSON.',
    ]);
});

it('validates numeric fields to create a leave type', function () {
    $response = $this->post(route('hr.leave-type.store'), [
        'annualQuota' => 'not-a-number',
        'carryOverLimit' => 'not-a-number',
    ]);

    $response->assertInvalid([
        'annualQuota' => 'Kuota vjetore duhet të jetë një numër i plotë.',
        'carryOverLimit' => 'Limiti i bartjes duhet të jetë një numër.',
    ]);
});

/*
* UPDATE LEAVE TYPE
*/
it('successfully open the edit page with valid id and return leave type data to the view', function () {
    $leaveType = LeaveType::factory()->create();

    $response = $this->get(route('hr.leave-type.edit', [
        'leaveTypeID' => $leaveType->leaveTypeID,
    ]));

    $response->assertOK()
        ->assertViewIs('Hr.LeaveType.edit')
        ->assertViewHas('leaveType');
});

it("doesn't open the edit page with invalid id", function () {
    $response = $this->get(route('hr.leave-type.edit', [
        // without the data
    ]));
    $response->assertRedirectToRoute('hr.leave-type.index')
        ->assertSessionHasErrors([
            'leaveTypeID' => 'ID e llojit të lejes është e detyrueshme.',
        ]);

    $response = $this->get(route('hr.leave-type.edit', [
        'leaveTypeID' => 'asdf',
    ]));
    $response->assertRedirectToRoute('hr.leave-type.index')
        ->assertSessionHasErrors([
            'leaveTypeID' => 'ID e llojit të lejes duhet të jetë një numër i plotë.',
        ]);

    $response = $this->get(route('hr.leave-type.edit', [
        'leaveTypeID' => 0,
    ]));
    $response->assertRedirectToRoute('hr.leave-type.index')
        ->assertSessionHasErrors([
            'leaveTypeID' => 'ID e llojit të lejes duhet të jetë më e madhe se 0.',
        ]);

    $response = $this->get(route('hr.leave-type.edit', [
        'leaveTypeID' => 99,
    ]));
    $response->assertRedirectToRoute('hr.leave-type.index')
        ->assertSessionHasErrors([
            'leaveTypeID' => 'Lloji i lejes me këtë ID nuk egziston.',
        ]);
});

it('successfully retrieves a leave type by ID', function () {
    $leaveType = LeaveType::factory()->create();

    $result = app(\App\Services\LeaveService::class)
        ->getLeaveType($leaveType->leaveTypeID);

    expect($result)
        ->toBeInstanceOf(LeaveType::class)
        ->leaveTypeID->toBe($leaveType->leaveTypeID);
});

it('handles missing leave type gracefully', function () {
    // Arrange: Non-existent ID
    $nonExistentId = 9999;

    // Assert: Verify exception and logging
    Log::shouldReceive('error')
        ->once()
        ->with("LeaveType not found: ID {$nonExistentId}");

    // Act & Assert
    expect(fn () => app(\App\Services\LeaveService::class)->getLeaveType($nonExistentId))
        ->toThrow(\RuntimeException::class, 'Lloji i pushimit nuk u gjet.');
});

it('successfully updates a leave type with valid data', function () {
    $leaveType = LeaveType::factory()->create();

    $response = $this->patch(route('hr.leave-type.update'), [
        'leaveTypeID' => $leaveType->leaveTypeID,
        'name' => 'changed name',
        'description' => 'no description',
        'isPaid' => false,
        'requiresApproval' => true,
    ]);

    $response->assertFound()
        ->assertRedirect(route('hr.leave-type.index'))
        ->with([
            'success' => 'Lloji i pushimit përditësohet me sukses.',
        ]);

    $leaveType->refresh();

    $this->assertDatabaseHas('leave_types', [
        'leaveTypeID' => $leaveType->leaveTypeID,
        'name' => 'changed name',
        'description' => 'no description',
        'isPaid' => false,
        'requiresApproval' => true,
    ]);

    expect($leaveType->name)->toBe('changed name');
    expect($leaveType->description)->toBe('no description');
    expect($leaveType->isPaid)->toBe(0);
    expect($leaveType->requiresApproval)->toBe(1);
});

it('rejects all invalid leave type update attempts', function () {
    // Create test data
    $existingLeaveType = LeaveType::factory()->create(['name' => 'Existing Leave']);
    $leaveType = LeaveType::factory()->create();
    $longDescription = str_repeat('a', 501);

    // Test cases with expected errors
    $testCases = [
        [
            'input' => [],
            'errors' => [
                'leaveTypeID' => 'ID e llojit të lejes është e detyrueshme.',
                'name' => 'Emri i llojit të lejes është i detyrueshëm.',
                'isPaid' => 'Statusi i pagesës është i detyrueshëm.',
                'requiresApproval' => 'Kërkohet aprovim është i detyrueshëm.',
            ],
        ],
        [
            'input' => [
                'leaveTypeID' => 'asdf',
                'name' => 990,
                'description' => 999,
                'isPaid' => 99,
                'requiresApproval' => 99,
            ],
            'errors' => [
                'leaveTypeID' => 'ID e llojit të lejes duhet të jetë një numër i plotë.',
                'name' => 'Emri i llojit të lejes duhet të jetë një varg tekstual.',
                'description' => 'Përshkrimi duhet të jetë një varg tekstual.',
                'isPaid' => 'Statusi i pagesës duhet të jetë \'true\' ose \'false\'.',
                'requiresApproval' => 'Kërkohet aprovim duhet të jetë \'true\' ose \'false\'.',
            ],
        ],
        [
            'input' => [
                'leaveTypeID' => 0,
                'name' => 'Test Leave',
                'isPaid' => true,
                'requiresApproval' => false,
            ],
            'errors' => [
                'leaveTypeID' => 'ID e llojit të lejes duhet të jetë më e madhe se 0.',
            ],
        ],
        [
            'input' => [
                'leaveTypeID' => $leaveType->leaveTypeID,
                'name' => 'Existing Leave', // Duplicate name
                'isPaid' => true,
                'requiresApproval' => false,
            ],
            'errors' => [
                'name' => 'Ekziston tashmë një lloj leje me këtë emër.',
            ],
        ],
        [
            'input' => [
                'leaveTypeID' => 9999, // Doesn't exist
                'name' => 'Test Leave',
                'isPaid' => true,
                'requiresApproval' => false,
            ],
            'errors' => [
                'leaveTypeID' => 'Lloji i lejes me këtë ID nuk egziston.',
            ],
        ],
        [
            'input' => [
                'leaveTypeID' => $leaveType->leaveTypeID,
                'name' => 'Valid Name',
                'description' => $longDescription,
                'isPaid' => true,
                'requiresApproval' => false,
            ],
            'errors' => [
                'description' => 'Përshkrimi nuk mund të jetë më i gjatë se 500 karaktere.',
            ],
        ],
    ];

    foreach ($testCases as $case) {
        $response = $this->patch(route('hr.leave-type.update'), $case['input']);
        $response->assertRedirect(route('hr.leave-type.index'))
            ->assertSessionHasErrors($case['errors']);
    }
});

it('handles runtime exceptions during leave type update', function () {
    $leaveType = LeaveType::factory()->create();

    $validData = [
        'leaveTypeID' => $leaveType->leaveTypeID,
        'name' => 'Test Leave',
        'description' => 'Test Description',
        'isPaid' => true,
        'requiresApproval' => false,
    ];

    $this->mock(\App\Services\LeaveService::class, function ($mock) {
        $mock->shouldReceive('updateLeaveType')
            ->andThrow(new \RuntimeException('Update failed due to system error'));
    });

    $response = $this->patch(route('hr.leave-type.update'), $validData);

    $response->assertRedirect(route('hr.leave-type.index'))
        ->assertSessionHas('error', 'Update failed due to system error');
});
