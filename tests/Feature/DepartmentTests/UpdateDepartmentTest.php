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
        'password' => Hash::make('test1234'),
    ]);

    $role = Role::create(['roleName' => 'admin']);
    $this->roleManager = Role::create(['roleName' => 'manager']);

    EmployeeRole::create([
        'employeeID' => $employee['employeeID'],
        'roleID' => $role['roleID'],
    ]);
    EmployeeRole::create([
        'employeeID' => $manager->employeeID,
        'roleID' => $this->roleManager->roleID,
    ]);

    $this->dep = Department::create([
        'departmentName' => 'test',
        'supervisorID' => $manager->employeeID,
    ]);

    Auth::guard('employee')->login($employee);
});

it('tests update department function with valid data', function () {
    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => $this->dep->departmentID,
        'newDepartmentName' => 'new name',
    ]);

    $this->assertDatabaseHas('departments', [
        'departmentID' => $this->dep->departmentID,
        'departmentName' => 'new name',
    ]);

    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHas('success', 'Të dhënat e departamentit janë përditësuar me sukses.');

    $newManager = Employee::create([
        'firstName' => 'new',
        'lastName' => 'new12',
        'email' => 'new@gmail.com',
        'phone' => '045087232',
        'password' => Hash::make('test1234'),
    ]);
    EmployeeRole::create([
        'employeeID' => $newManager->employeeID,
        'roleID' => $this->roleManager->roleID,
    ]);

    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => $this->dep->departmentID,
        'newDepartmentName' => 'new name 2',
        'newSupervisorID' => $newManager->employeeID,
    ]);

    $this->assertDatabaseHas('departments', [
        'departmentID' => $this->dep->departmentID,
        'departmentName' => 'new name 2',
        'supervisorID' => $newManager->employeeID,
    ]);

    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHas('success', 'Të dhënat e departamentit janë përditësuar me sukses.');
});

it('tests update department function with invalid data', function () {
    $response = $this->patch(route('admin.department.update'), []);
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'ID e departamentit është e detyrueshme.',
        'newDepartmentName' => 'Emri i ri i departamentit është i detyrueshëm.',
    ]);

    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => 'asdf',
        'newDepartmentName' => 123,
        'newSupervisorID' => 'asdf',
    ]);
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'ID e departamentit duhet të jetë një numër i plotë.',
        'newDepartmentName' => 'Emri i ri i departamentit duhet të jetë një varg tekstual.',
        'newSupervisorID' => 'ID e menaxherit duhet të jetë një numër i plotë.',
    ]);

    Department::create(['departmentName' => 'departamenti sokav']);

    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => 0,
        'newDepartmentName' => 'departamenti sokav',
        'newSupervisorID' => 0,
    ]);
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'ID e departamentit duhet të jetë më e madhe se 0.',
        'newDepartmentName' => 'Ekziston tashmë një departament me këtë emër.',
        'newSupervisorID' => 'ID e menaxherit duhet të jetë më e madhe se 0.',
    ]);

    $response = $this->patch(route('admin.department.update'), [
        'departmentID' => 99,
        'newDepartmentName' => 'departamenti sokav',
        'newSupervisorID' => 99,
    ]);
    $response->assertRedirect(route('admin.department.index'));
    $response->assertSessionHasErrors([
        'departmentID' => 'Departamenti me këtë ID nuk egziston.',
        'newDepartmentName' => 'Ekziston tashmë një departament me këtë emër.',
        'newSupervisorID' => 'Menaxheri me këtë ID nuk egziston.',
    ]);
});
