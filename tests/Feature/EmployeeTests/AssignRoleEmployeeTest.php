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

it('assign role to an employee with valid data', function (): void {
    $newRole = Role::create([
        'roleName' => 'NewRole',
    ]);

    $response = $this->patch(route('admin.employee.assign-role'), [
        'roleID' => $newRole->roleID,
        'employeeID' => $this->employee->employeeID,
    ]);

    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHas('success', 'Roli i punonjësit u ndryshua me sukses.');
});

it('assign role to an employee with invalid data', function (): void {
    $newRole = Role::create([
        'roleName' => 'NewRole',
    ]);

    $response = $this->patch(route('admin.employee.assign-role'), []);
    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit është e detyrueshme.',
        'roleID' => 'ID e rolit është e detyrueshme.',
    ]);

    $response = $this->patch(route('admin.employee.assign-role'), [
        'employeeID' => 'sat',
        'roleID' => 'sat',
    ]);
    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit duhet të jetë një numër i plotë.',
        'roleID' => 'ID e rolit duhet të jetë një numër i plotë.',
    ]);

    $response = $this->patch(route('admin.employee.assign-role'), [
        'employeeID' => 0,
        'roleID' => -1,
    ]);
    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit duhet të jetë më e madhe se 0.',
        'roleID' => 'ID e rolit duhet të jetë më e madhe se 0.',
    ]);

    $response = $this->patch(route('admin.employee.assign-role'), [
        'employeeID' => 999,
        'roleID' => 999,
    ]);
    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors([
        'employeeID' => 'Punonjësi me këtë ID nuk egziston.',
        'roleID' => 'Roli me këtë ID nuk egziston.',
    ]);
});
