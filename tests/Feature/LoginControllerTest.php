<?php

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses()->group('authentication');
uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');

    $this->employeeRole = Role::create(['roleName' => 'employee']);
    $this->adminRole = Role::create(['roleName' => 'admin']);
    $this->hrRole = Role::create(['roleName' => 'hr']);
    $this->managerRole = Role::create(['roleName' => 'manager']);
});

it('shows login page to unauthenticated users', function (): void {
    $response = $this->get(route('loginPage'));

    $response->assertOk();
    $response->assertViewIs('Auth.Login');
});

it('redirects authenticated users away from login page', function (): void {
    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $employee->employeeID,
        'roleID' => $this->employeeRole->roleID,
    ]);

    Auth::guard('employee')->login($employee);

    $response = $this->get(route('loginPage'));
    $response->assertRedirect(route('dashboard'));
});

it('allows employee to login with valid credentials', function (): void {
    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '045789123',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $employee->employeeID,
        'roleID' => $this->employeeRole->roleID,
    ]);

    $response = $this->post(route('login'), [
        'phone' => '045789123',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('employee.dashboard'));
    $this->assertAuthenticatedAs($employee, 'employee');
});

it('allows admin to login with valid credentials', function (): void {
    $admin = Employee::create([
        'firstName' => 'Admin',
        'lastName' => 'User',
        'email' => 'admin@example.com',
        'phone' => '045789123',
        'password' => Hash::make('admin123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $admin->employeeID,
        'roleID' => $this->adminRole->roleID,
    ]);

    $response = $this->post(route('login'), [
        'phone' => '045789123',
        'password' => 'admin123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin, 'employee');
});

it('allows HR to login with valid credentials', function (): void {
    $hr = Employee::create([
        'firstName' => 'HR',
        'lastName' => 'User',
        'email' => 'hr@example.com',
        'phone' => '045789123',
        'password' => Hash::make('hr123456'),
    ]);

    EmployeeRole::create([
        'employeeID' => $hr->employeeID,
        'roleID' => $this->hrRole->roleID,
    ]);

    $response = $this->post(route('login'), [
        'phone' => '045789123',
        'password' => 'hr123456',
    ]);

    $response->assertRedirect(route('hr.dashboard'));
    $this->assertAuthenticatedAs($hr, 'employee');
});

it('allows manager to login with valid credentials', function (): void {
    $manager = Employee::create([
        'firstName' => 'Manager',
        'lastName' => 'User',
        'email' => 'manager@example.com',
        'phone' => '045789123',
        'password' => Hash::make('manager123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $manager->employeeID,
        'roleID' => $this->managerRole->roleID,
    ]);

    $response = $this->post(route('login'), [
        'phone' => '045789123',
        'password' => 'manager123',
    ]);

    $response->assertRedirect(route('manager.dashboard'));
    $this->assertAuthenticatedAs($manager, 'employee');
});

it('rejects login with invalid credentials', function (): void {
    Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login'), [
        'phone' => '123456789',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['phone']);
    $this->assertGuest('employee');
});

it('requires phone and password fields', function (): void {
    $response = $this->post(route('login'), []);

    $response->assertSessionHasErrors([
        'phone' => 'Fusha e numrit të telefonit është e detyrueshme.',
        'password' => 'Fusha e fjalëkalimit është e detyrueshme.',
    ]);
});

it('logs out authenticated users', function (): void {
    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $employee->employeeID,
        'roleID' => $this->employeeRole->roleID,
    ]);

    Auth::guard('employee')->login($employee);
    $this->assertAuthenticated('employee');

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('loginPage'));
    $response->assertSessionHas('success', 'You have been logged out.');
    $this->assertGuest('employee');
});

it('redirects to proper dashboard based on role', function (): void {
    // Test employee
    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
        'password' => Hash::make('password123'),
    ]);
    EmployeeRole::create([
        'employeeID' => $employee->employeeID,
        'roleID' => $this->employeeRole->roleID,
    ]);

    Auth::guard('employee')->login($employee);
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('employee.dashboard'));

    // Test admin
    $admin = Employee::create([
        'firstName' => 'Admin',
        'lastName' => 'User',
        'email' => 'admin@example.com',
        'phone' => '987654321',
        'password' => Hash::make('admin123'),
    ]);
    EmployeeRole::create([
        'employeeID' => $admin->employeeID,
        'roleID' => $this->adminRole->roleID,
    ]);

    Auth::guard('employee')->login($admin);
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('admin.dashboard'));
});
