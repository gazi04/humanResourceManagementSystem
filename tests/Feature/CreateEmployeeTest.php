<?php

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\EmployeeRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Use the RefreshDatabase trait
uses(RefreshDatabase::class);

beforeEach(function () {
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

it('creates an employee with valid data', function () {
    // Create a department for the foreign key constraint
    $department = Department::create([
        'departmentID' => 1,
        'departmentName' => 'IT Department',
    ]);

    // Simulate a POST request with valid data
    $response = $this->post(route('admin.employee.create'), [
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

    // Assert that the employee was created in the database
    $this->assertDatabaseHas('employees', [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '045123456',
        'hireDate' => '2023-10-01',
        'jobTitle' => 'Software Engineer',
        'status' => 'Active',
        'departmentID' => $department->departmentID,
    ]);

    // Assert that the user is redirected to the employee index page
    $response->assertRedirect(route('admin.employee.index'));

    // Assert that the password is hashed
    $employee = Employee::where('email', 'john.doe@example.com')->first();
    expect(Hash::check('password123', $employee->password))->toBeTrue();
});

it('fails to create an employee with invalid data', function () {
    // Simulate a POST request with invalid data (missing required fields)
    $response = $this->post(route('admin.employee.create'), [
        // Missing required fields
    ]);

    // Assert that the user is redirected back with validation errors
    $response->assertRedirect(route('admin.employee.index'));

    // Assert that the specific validation error messages are present in the session
    $response->assertSessionHasErrors([
        'firstName' => 'Emri është i detyrueshëm.',
        'lastName' => 'Mbiemri është i detyrueshëm.',
        'email' => 'Adresa email është e detyrueshme.',
        'password' => 'Fjalëkalimi është i detyrueshëm.',
        'phone' => 'Fusha e numrit të telefonit është e detyrueshme.',
        'hireDate' => 'Data e punësimit është e detyrueshme.',
        'jobTitle' => 'Titulli i punës është i detyrueshëm.',
        'status' => 'Statusi është i detyrueshëm.',
        'departmentID' => 'ID e departamentit është e detyrueshme.',
    ]);

    // Assert that no employee was created in the database
    $this->assertDatabaseCount('employees', 1); // Only the admin user created in beforeEach
});

it('fails to create an employee with a duplicate email', function () {
    // Create a department for the foreign key constraint
    $department = Department::create([
        'departmentID' => 1,
        'departmentName' => 'IT Department',
    ]);

    // Create an existing employee
    Employee::create([
        'firstName' => 'Jane',
        'lastName' => 'Doe',
        'email' => 'jane.doe@example.com',
        'password' => Hash::make('password123'),
        'phone' => '045654321',
        'hireDate' => '2023-10-01',
        'jobTitle' => 'HR Manager',
        'status' => 'Active',
        'departmentID' => 1,
    ]);

    // Simulate a POST request with a duplicate email
    $response = $this->post(route('admin.employee.create'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'jane.doe@example.com', // Duplicate email
        'password' => 'password123',
        'phone' => '045123456',
        'hireDate' => '2023-10-01',
        'jobTitle' => 'Software Engineer',
        'status' => 'Active',
        'departmentID' => 1,
    ]);

    // Assert that the user is redirected back with validation errors
    $response->assertRedirect(route('admin.employee.index'));

    // Assert that the specific validation error message is present in the session
    $response->assertSessionHasErrors([
        'email' => 'Kjo adresë email është tashmë e përdorur.',
    ]);

    // Assert that no new employee was created in the database
    $this->assertDatabaseCount('employees', 2); // Admin user + existing employee
});
