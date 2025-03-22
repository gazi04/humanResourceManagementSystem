<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Use the RefreshDatabase trait
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

    $this->manager = Employee::create([
        'firstName' => 'menager',
        'lastName' => 'halili',
        'email' => 'gazi@gmail.com',
        'phone' => '045918376',
        'password' => Hash::make('gazi04'),
    ]);

    $role = Role::create(['roleName' => 'admin']);
    $managerRole = Role::create(['roleName' => 'manager']);

    EmployeeRole::create([
        'employeeID' => $employee['employeeID'],
        'roleID' => $role['roleID'],
    ]);

    EmployeeRole::create([
        'employeeID' => $this->manager->employeeID,
        'roleID' => $managerRole->roleID,
    ]);

    Auth::guard('employee')->login($employee);
});

it('create department with valid data', function() {
    $response = $this->post(route('admin.department.store'), [
        'departmentName' => 'testDEp',
        'supervisorID' => $this->manager->employeeID,
    ]);

    $this->assertDatabaseHas('departments', [
        'departmentName' => 'testDEp',
        'supervisorID' => $this->manager->employeeID,
    ]);

    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHas('success', 'Departamenti është krijuar me sukses.');
});
