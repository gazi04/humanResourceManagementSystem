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
    $admin = Employee::create([
        'firstName' => 'gazi',
        'lastName' => 'halili',
        'email' => 'gaz@gmail.com',
        'phone' => '045618376',
        'password' => Hash::make('gazi04'),
    ]);

    $adminRole = Role::create(['roleName' => 'admin']);
    EmployeeRole::create([
        'employeeID' => $admin->employeeID,
        'roleID' => $adminRole->roleID,
    ]);

    Auth::guard('employee')->login($admin);

    $department = Department::create([
        'departmentName' => 'IT Department',
    ]);

    $employee1 = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => Hash::make('gazi04'),
        'phone' => '123456789',
        'jobTitle' => 'Developer',
        'departmentID' => $department->departmentID,
    ]);

    $employee2 = Employee::create([
        'firstName' => 'Jane',
        'lastName' => 'Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '987654321',
        'password' => Hash::make('gazi04'),
        'jobTitle' => 'HR Manager',
        'departmentID' => $department->departmentID,
    ]);

    $employeeRole = Role::create(['roleName' => 'employee']);
    $managerRole = Role::create(['roleName' => 'manager']);
    EmployeeRole::create([
        'employeeID' => $employee1->employeeID,
        'roleID' => $employeeRole->roleID,
    ]);
    EmployeeRole::create([
        'employeeID' => $employee2->employeeID,
        'roleID' => $managerRole->roleID,
    ]);
});

it('requires search term', function (): void {
    $response = $this->get(route('admin.employee.search'));

    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors(['searchingTerm']);
});

it('searches employees by first name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'John']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');
});

it('searches employees by last name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'Doe']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');
});

it('searches employees by email', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'john.doe@example.com']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');
});

it('searches employees by phone number', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => '123456789']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');
});

it('searches employees by job title', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'Developer']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');
});

it('searches employees by department name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'IT Department']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertSee('Jane Smith');
});

it('searches employees by role name', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'employee']
    );

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertDontSee('Jane Smith');

    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'manager']
    );

    $response->assertStatus(200);
    $response->assertSee('Jane Smith');
    $response->assertDontSee('John Doe');
});

it('returns empty results when no matches found', function (): void {
    $response = $this->call(
        'GET',
        route('admin.employee.search'),
        ['searchingTerm' => 'Nonexistent']
    );

    $response->assertStatus(200);
    $response->assertDontSee('John Doe');
    $response->assertDontSee('Jane Smith');
});
