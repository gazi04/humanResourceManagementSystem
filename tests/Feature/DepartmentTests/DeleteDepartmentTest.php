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

    $role = Role::create(['roleName' => 'admin']);

    EmployeeRole::create([
        'employeeID' => $employee['employeeID'],
        'roleID' => $role['roleID'],
    ]);

    $this->dep = Department::create(['departmentName' => 'test']);

    Auth::guard('employee')->login($employee);
});

it('tests the delete department function with valid data', function (): void {
    $department = Department::create(['departmentName' => 'bla']);
    $response = $this->delete(route('admin.department.destroy'), [
        'departmentID' => $department->departmentID,
    ]);

    $this->assertDatabaseMissing('departments', [
        'departmentID' => $department->departmentID,
        'departmentName' => 'test',
    ]);

    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHas('success', 'Departamenti është fshirë me sukses.');
});

it('tests the delete department function with invalid data', function (): void {
    $response = $this->delete(route('admin.department.destroy', []));
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'ID e departamentit është e detyrueshme.',
    ]);

    $response = $this->delete(route('admin.department.destroy', [
        'departmentID' => 'test',
    ]));
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'ID e departamentit duhet të jetë një numër i plotë.',
    ]);

    $response = $this->delete(route('admin.department.destroy', [
        'departmentID' => 0,
    ]));
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'ID e departamentit duhet të jetë më e madhe se 0.',
    ]);

    $response = $this->delete(route('admin.department.destroy', [
        'departmentID' => 999,
    ]));
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'Departamenti me këtë ID nuk egziston.',
    ]);
});
