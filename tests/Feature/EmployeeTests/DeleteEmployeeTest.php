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
});

it('delete employee with valid data', function (): void {
    $department = Department::create([
        'departmentID' => 1,
        'departmentName' => 'IT Department',
    ]);

    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'phone' => '045123456',
        'hireDate' => '2023-10-01',
        'jobTitle' => 'Software Engineer',
        'status' => 'Active',
        'departmentID' => $department->departmentID,
    ]);

    $response = $this->delete(route('admin.employee.destroy'), [
        'employeeID' => $employee->employeeID,
        'email' => $employee->email,
    ]);

    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHas('success', 'Punonjësi është fshirë me sukses.');
});

it('delete employee with invalid data', function (): void {
    $department = Department::create([
        'departmentID' => 1,
        'departmentName' => 'IT Department',
    ]);

    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'phone' => '045123456',
        'hireDate' => '2023-10-01',
        'jobTitle' => 'Software Engineer',
        'status' => 'Active',
        'departmentID' => $department->departmentID,
    ]);

    /* Testing required rule in the DeleteEmployeeRequest */
    $response = $this->delete(route('admin.employee.destroy'), [
        //
    ]);

    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit është e detyrueshme.',
        'email' => 'Adresa email është e detyrueshme.',
    ]);

    /* Testing the rules if the data is valid */
    $response = $this->delete(route('admin.employee.destroy', [
        'employeeID' => 'gazi',
        'email' => 'gazi',
    ]));

    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit duhet të jetë një numër i plotë.',
        'email' => 'Adresa email nuk është e vlefshme.',
    ]);

    /* Testing if the given employeeID and email they belong to the same employee */
    $response = $this->delete(route('admin.employee.destroy'), [
        'employeeID' => $employee->employeeID,
        'email' => 'gazi@gmail.com',
    ]);

    $response->assertRedirect(route('admin.employee.index'));
    $response->assertSessionHas('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
});
