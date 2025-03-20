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

it('update employee with valid data', function (): void {
    /* $department = Department::create(['departmentName' => 'IT']); */
    $secondDepartment = Department::create(['departmentName' => 'AD']);

    /* $employee = Employee::create([ */
    /*    'firstName' => 'FIlan', */
    /*    'lastName' => 'Fisteku', */
    /*    'email' => 'fila@gmail.com', */
    /*    'password' => Hash::make('fila123'), */
    /*    'phone' => '045890123', */
    /*    'departmentID' => $this->department->departmentID, */
    /* ]); */

    $response = $this->patch(route('admin.employee.update'), [
        'employeeID' => $this->employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'test@gmail.com',
        'password' => 'test1234',
        'phone' => '045987654',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => $secondDepartment->departmentID,
    ]);

    $employee = Employee::where('employeeID', $this->employee->employeeID)->first();

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

it('update employee with invalid data', function (): void {
    $response = $this->patch(route('admin.employee.update'), []);

    $response->assertRedirect(route('admin.employee.index'));

    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit është e detyrueshme.',
        'firstName' => 'Emri është i detyrueshëm.',
        'lastName' => 'Mbiemri është i detyrueshëm.',
        'email' => 'Adresa email është e detyrueshme.',
        'phone' => 'Fusha e numrit të telefonit është e detyrueshme.',
        'jobTitle' => 'Titulli i punës është i detyrueshëm.',
        'status' => 'Statusi është i detyrueshëm.',
        'departmentID' => 'ID e departamentit është e detyrueshme.',
    ]);
});

it('returns validation errors for invalid or missing data', function (): void {
    $response = $this->patch(route('admin.employee.update'), []);

    $response->assertRedirect(route('admin.employee.index'));

    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit është e detyrueshme.',
        'firstName' => 'Emri është i detyrueshëm.',
        'lastName' => 'Mbiemri është i detyrueshëm.',
        'email' => 'Adresa email është e detyrueshme.',
        'phone' => 'Fusha e numrit të telefonit është e detyrueshme.',
        'jobTitle' => 'Titulli i punës është i detyrueshëm.',
        'status' => 'Statusi është i detyrueshëm.',
        'departmentID' => 'ID e departamentit është e detyrueshme.',
    ]);
});

it('returns validation errors for invalid email format', function (): void {
    $response = $this->patch(route('admin.employee.update'), [
        'employeeID' => $this->employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'invalid-email',
        'phone' => '045987654',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => $this->department->departmentID,
    ]);

    $response->assertRedirect(route('admin.employee.index'));

    $response->assertSessionHasErrors([
        'email' => 'Adresa email nuk është e vlefshme.',
    ]);
});

it('returns validation errors for duplicate email', function (): void {
    $anotherEmployee = Employee::create([
        'firstName' => 'Another',
        'lastName' => 'Employee',
        'email' => 'another@gmail.com',
        'password' => Hash::make('another123'),
        'phone' => '045123456',
        'departmentID' => $this->department->departmentID,
    ]);

    $response = $this->patch(route('admin.employee.update'), [
        'employeeID' => $this->employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'another@gmail.com',
        'phone' => '045987654',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => $this->department->departmentID,
    ]);

    $response->assertRedirect(route('admin.employee.index'));

    $response->assertSessionHasErrors([
        'email' => 'Kjo adresë email është tashmë e përdorur.',
    ]);
});

it('returns validation errors for invalid phone number format', function (): void {
    $response = $this->patch(route('admin.employee.update'), [
        'employeeID' => $this->employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'test@gmail.com',
        'phone' => '123456',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => $this->department->departmentID,
    ]);

    $response->assertRedirect(route('admin.employee.index'));

    $response->assertSessionHasErrors([
        'phone' => 'Numri i telefonit nuk është i vlefshëm. Duhet të fillojë me +383 ose 0 dhe të përmbajë 7 shifra pas prefiksit.',
    ]);
});

it('returns validation errors for invalid department ID', function (): void {
    $response = $this->patch(route('admin.employee.update'), [
        'employeeID' => $this->employee->employeeID,
        'firstName' => 'test',
        'lastName' => 'testMbi',
        'email' => 'test@gmail.com',
        'phone' => '045987654',
        'jobTitle' => 'BLa',
        'status' => 'On Leave',
        'departmentID' => 999,
    ]);

    $response->assertRedirect(route('admin.employee.index'));

    $response->assertSessionHasErrors([
        'departmentID' => 'Departamenti me këtë ID nuk egziston.',
    ]);
});
