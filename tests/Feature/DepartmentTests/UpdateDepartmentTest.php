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
    $manager = Employee::create([
        'firstName' => 'etst',
        'lastName' => 'bla',
        'email' => 'tes@gmail.com',
        'phone' => '045987232',
        'password' => Hash::make('test1234')
    ]);


    $role = Role::create(['roleName' => 'admin']);
    $this->roleManager = Role::create(['roleName' => 'manager']);

    EmployeeRole::create([
        'employeeID' => $employee['employeeID'],
        'roleID' => $role['roleID'],
    ]);
    EmployeeRole::create([
        'employeeID' => $manager->employeeID,
        'roleID' => $this->roleManager->roleID
    ]);

    $this->dep = Department::create([
        'departmentName' => 'test',
        'supervisorID' => $manager->employeeID
    ]);

    Auth::guard('employee')->login($employee);
});

it('update department with valid data', function() {
    // Update department name only
    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => $this->dep->departmentID,
        'newDepartmentName' => 'new name'
    ]);

    // Assert that the department name was updated
    $this->assertDatabaseHas('departments', [
        'departmentID' => $this->dep->departmentID,
        'departmentName' => 'new name'
    ]);

    // Assert redirect and success message
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHas('success', 'Të dhënat e departamentit janë përditësuar me sukses.');

    // Create a new manager
    $newManager = Employee::create([
        'firstName' => 'new',
        'lastName' => 'new12',
        'email' => 'new@gmail.com',
        'phone' => '045087232',
        'password' => Hash::make('test1234')
    ]);
    EmployeeRole::create([
        'employeeID' => $newManager->employeeID,
        'roleID' => $this->roleManager->roleID,
    ]);

    // Update department name and supervisor
    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => $this->dep->departmentID,
        'newDepartmentName' => 'new name 2',
        'newSupervisorID' => $newManager->employeeID
    ]);

    // Assert that the department name and supervisor were updated
    $this->assertDatabaseHas('departments', [
        'departmentID' => $this->dep->departmentID,
        'departmentName' => 'new name 2',
        'supervisorID' => $newManager->employeeID
    ]);

    // Assert redirect and success message
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHas('success', 'Të dhënat e departamentit janë përditësuar me sukses.');
});
