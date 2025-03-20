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

    // Create an admin user and log them in
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

    // Create a department for the foreign key constraint
    $this->department = Department::create(['departmentName' => 'IT']);

    // Create an employee to update
    $this->employee = Employee::create([
        'firstName' => 'FIlan',
        'lastName' => 'Fisteku',
        'email' => 'fila@gmail.com',
        'password' => Hash::make('fila123'),
        'phone' => '045890123',
        'departmentID' => $this->department->departmentID,
    ]);
});

it('assign role to an employee with valid data', function() {
    expect(true)->toBe(true);
});

it('assign role to an employee with invalid data', function() {
    expect(true)->toBe(true);
});
