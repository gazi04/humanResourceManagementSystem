<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');

    $employee = Employee::create([
        'firstName' => 'gazi',
        'lastName' => 'halili',
        'email' => 'gaz@gmail.com',
        'phone' => '045618376',
        'password' => Hash::make('gazi04'),
    ]);

    $role = Role::create(['roleName' => 'admin']);

    EmployeeRole::create([
        'employeeID' => $employee['employeeID'],
        'roleID' => $role['roleID'],
    ]);

    Auth::guard('employee')->login($employee);

    $supervisor1 = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '045123456',
        'password' => Hash::make('password123'),
    ]);

    $supervisor2 = Employee::create([
        'firstName' => 'Jane',
        'lastName' => 'Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '045654321',
        'password' => Hash::make('password123'),
    ]);

    Department::create([
        'departmentName' => 'gazi',
        'supervisorID' => $supervisor1->employeeID,
    ]);

    Department::create([
        'departmentName' => 'bla',
        'supervisorID' => $supervisor2->employeeID,
    ]);
});

it('searches departments by department name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.department.search'),
        ['searchingTerm' => 'zi']
    );

    $response->assertStatus(200);
    $response->assertSee('gazi');
    $response->assertDontSee('bla');
});

it('searches departments by supervisor first name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.department.search'),
        ['searchingTerm' => 'John']
    );

    $response->assertSee('gazi');
    $response->assertDontSee('bla');
});

it('searches departments by supervisor last name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.department.search'),
        ['searchingTerm' => 'Smith'],
    );

    $response->assertSee('bla');
    $response->assertDontSee('gazi');
});

it('returns validation error for empty search term', function (): void {
    $response = $this->call(
        'GET',
        route('admin.department.search'),
        ['searchingTerm' => ''],
    );

    $response->assertRedirect(route('admin.department.index'));

    $response->assertSessionHasErrors([
        'searchingTerm' => 'Termi i kërkimit është i detyrueshëm.',
    ]);
});

it('returns validation error for invalid search term', function (): void {
    $response = $this->call(
        'GET',
        route('admin.department.search'),
        ['searchingTerm' => 999],
    );

    $response->assertRedirect(route('admin.department.index'));

    $response->assertSessionHasErrors([
        'searchingTerm' => 'Termi i kërkimit duhet të jetë një varg tekstual.',
    ]);
});

it('returns paginated search results', function (): void {
    for ($i = 1; $i <= 15; $i++) {
        Department::create([
            'departmentName' => 'Department '.$i,
            'supervisorID' => Employee::create([
                'firstName' => 'Supervisor '.$i,
                'lastName' => 'Lastname '.$i,
                'email' => 'supervisor'.$i.'@example.com',
                'phone' => '04512345'.$i,
                'password' => Hash::make('password123'),
            ])->employeeID,
        ]);
    }

    $response = $this->call(
        'GET',
        route('admin.department.search'),
        ['searchingTerm' => 'Department'],
    );

    $response->assertSee('Department 1');
    $response->assertSee('Department 10');
    $response->assertDontSee('Department 15');
});
