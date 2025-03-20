<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use App\Services\Interfaces\EmployeeServiceInterface;
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
});

it('update employee with valid data', function(): void {
    $department = Department::create(['departmentName' => 'IT']);
    $secondDepartment = Department::create(['departmentName' => 'AD']);

    $employee = Employee::create([
        'firstName' => 'FIlan',
        'lastName' => 'Fisteku',
        'email' => 'fila@gmail.com',
        'password' => Hash::make('fila123'),
        'phone' => '045890123',
        'departmentID' => $department->departmentID,
    ]);

    $response = $this->patch(route('admin.employee.update'), [
        'employeeID' => $employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'test@gmail.com',
        'password' => 'test1234',
        'phone' => '045987654',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => $secondDepartment->departmentID,
    ]);

    $employee = Employee::where('employeeID', $employee->employeeID)->first();

    $this->assertDatabaseHas('employees', [
        'employeeID' => $employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'test@gmail.com',
        'phone' => '045987654',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => $secondDepartment->departmentID,
    ]);

    expect($employee->firstName)->toBe('test');
});

it('update employee with invalid data', function():void {

});

/* it('updates an employee with valid data', function (): void { */
/*     // Create a department for the foreign key constraint */
/*     $department = Department::create([ */
/*         'departmentName' => 'IT Department', */
/*     ]); */
/**/
/*     // Create an employee to update */
/*     $employee = Employee::create([ */
/*         'firstName' => 'John', */
/*         'lastName' => 'Doe', */
/*         'email' => 'john.doe@example.com', */
/*         'password' => Hash::make('password123'), */
/*         'phone' => '045123456', */
/*         'hireDate' => '2023-10-01', */
/*         'jobTitle' => 'Software Engineer', */
/*         'status' => 'Active', */
/*         'departmentID' => $department->departmentID, */
/*     ]); */
/**/
/*     // Simulate a PATCH request with updated data */
/*     $response = $this->patch(route('admin.employee.update'), [ */
/*         'employeeID' => $employee->employeeID, */
/*         'firstName' => 'Jane', */
/*         'lastName' => 'Smith', */
/*         'email' => 'jane.smith@example.com', */
/*         'password' => 'newpassword123', */
/*         'phone' => '045654321', */
/*         'hireDate' => '2023-10-01', */
/*         'jobTitle' => 'Senior Software Engineer', */
/*         'status' => 'Inactive', */
/*         'departmentID' => $department->departmentID, */
/*     ]); */
/**/
/*     // Assert that the employee was updated in the database */
/*     $this->assertDatabaseHas('employees', [ */
/*         'employeeID' => $employee->employeeID, */
/*         'firstName' => 'Jane', */
/*         'lastName' => 'Smith', */
/*         'email' => 'jane.smith@example.com', */
/*         'phone' => '045654321', */
/*         'jobTitle' => 'Senior Software Engineer', */
/*         'status' => 'Inactive', */
/*         'departmentID' => $department->departmentID, */
/*     ]); */
/**/
/*     // Assert that the user is redirected to the employee index page */
/*     $response->assertRedirect(route('admin.employee.index')); */
/**/
/*     // Assert that the success message is present in the session */
/*     $response->assertSessionHas('success', 'Punonjësi u përditësua me sukses.'); */
/* }); */

/* it('fails to update an employee with an invalid employee ID', function (): void { */
/*     // Simulate a PATCH request with an invalid employee ID */
/*     $response = $this->patch(route('admin.employee.update'), [ */
/*         'employeeID' => 999, // Invalid employee ID */
/*         'firstName' => 'Jane', */
/*         'lastName' => 'Smith', */
/*         'email' => 'jane.smith@example.com', */
/*         'password' => 'newpassword123', */
/*         'phone' => '045654321', */
/*         'hireDate' => '2023-10-01', */
/*         'jobTitle' => 'Senior Software Engineer', */
/*         'status' => 'Inactive', */
/*         'departmentID' => 1, */
/*     ]); */
/**/
/*     // Assert that the user is redirected back with an error message */
/*     $response->assertRedirect(route('admin.employee.index')); */
/**/
/*     // Assert that the error message is present in the session */
/*     $response->assertSessionHas('error', 'Punonjësi nuk u gjet në bazën e të dhënave.'); */
/* }); */
/**/
/* it('fails to update an employee with invalid data', function (): void { */
/*     // Create a department for the foreign key constraint */
/*     $department = Department::create([ */
/*         'departmentID' => 1, */
/*         'departmentName' => 'IT Department', */
/*     ]); */
/**/
/*     // Create an employee to update */
/*     $employee = Employee::create([ */
/*         'firstName' => 'John', */
/*         'lastName' => 'Doe', */
/*         'email' => 'john.doe@example.com', */
/*         'password' => Hash::make('password123'), */
/*         'phone' => '045123456', */
/*         'hireDate' => '2023-10-01', */
/*         'jobTitle' => 'Software Engineer', */
/*         'status' => 'Active', */
/*         'departmentID' => $department->departmentID, */
/*     ]); */
/**/
/*     // Simulate a PATCH request with invalid data (missing required fields) */
/*     $response = $this->patch(route('admin.employee.update'), [ */
/*         'employeeID' => $employee->employeeID, */
/*         // Missing required fields */
/*     ]); */
/**/
/*     // Assert that the user is redirected back with validation errors */
/*     $response->assertRedirect(route('admin.employee.index')); */
/**/
/*     // Assert that the specific validation error messages are present in the session */
/*     $response->assertSessionHasErrors([ */
/*         'firstName' => 'Emri është i detyrueshëm.', */
/*         'lastName' => 'Mbiemri është i detyrueshëm.', */
/*         'email' => 'Adresa email është e detyrueshme.', */
/*         'password' => 'Fjalëkalimi është i detyrueshëm.', */
/*         'phone' => 'Fusha e numrit të telefonit është e detyrueshme.', */
/*         'jobTitle' => 'Titulli i punës është i detyrueshëm.', */
/*         'status' => 'Statusi është i detyrueshëm.', */
/*         'departmentID' => 'ID e departamentit është e detyrueshme.', */
/*     ]); */
/**/
/*     // Assert that the employee was not updated in the database */
/*     $this->assertDatabaseHas('employees', [ */
/*         'employeeID' => $employee->employeeID, */
/*         'firstName' => 'John', */
/*         'lastName' => 'Doe', */
/*         'email' => 'john.doe@example.com', */
/*         'phone' => '045123456', */
/*         'jobTitle' => 'Software Engineer', */
/*         'status' => 'Active', */
/*         'departmentID' => $department->departmentID, */
/*     ]); */
/* }); */
