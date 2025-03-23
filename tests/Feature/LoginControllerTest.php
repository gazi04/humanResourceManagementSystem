<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database after each test

    public function test_login_page_is_accessible(): void
    {
        $testResponse = $this->get(route('loginPage'));

        $testResponse->assertOk();
        $testResponse->assertViewIs('Auth.Login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $employee = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04'),
        ]);

        $role = Role::create(['roleName' => 'employee']);

        EmployeeRole::create([
            'employeeID' => $employee['employeeID'],
            'roleID' => $role['roleID'],
        ]);

        // Attempt to login
        $testResponse = $this->post(route('login'), [
            'phone' => '045618376',
            'password' => 'gazi04',
        ]);

        // Assert the user is redirected to the correct dashboard based on their role
        $testResponse->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($employee, 'employee');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        // Create an admin user in the database
        $admin = Employee::create([
            'firstName' => 'admin',
            'lastName' => 'user',
            'email' => 'admin@gmail.com',
            'phone' => '045618377',
            'password' => Hash::make('admin123'),
        ]);

        $role = Role::create(['roleName' => 'admin']);

        EmployeeRole::create([
            'employeeID' => $admin['employeeID'],
            'roleID' => $role['roleID'],
        ]);

        // Attempt to login
        $testResponse = $this->post(route('login'), [
            'phone' => '045618377',
            'password' => 'admin123',
        ]);

        // Assert the admin is redirected to the admin dashboard
        $testResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'employee');
    }

    public function test_hr_can_login_with_valid_credentials(): void
    {
        // Create an HR user in the database
        $hr = Employee::create([
            'firstName' => 'hr',
            'lastName' => 'user',
            'email' => 'hr@gmail.com',
            'phone' => '045618378',
            'password' => Hash::make('hr123456'),
        ]);

        $role = Role::create(['roleName' => 'hr']);

        EmployeeRole::create([
            'employeeID' => $hr['employeeID'],
            'roleID' => $role['roleID'],
        ]);

        // Attempt to login
        $testResponse = $this->post(route('login'), [
            'phone' => '045618378',
            'password' => 'hr123456',
        ]);

        // Assert the HR is redirected to the HR dashboard
        $testResponse->assertRedirect(route('hr.dashboard'));
        $this->assertAuthenticatedAs($hr, 'employee');
    }

    public function test_manager_can_login_with_valid_credentials(): void
    {
        // Create a manager user in the database
        $manager = Employee::create([
            'firstName' => 'manager',
            'lastName' => 'user',
            'email' => 'manager@gmail.com',
            'phone' => '045618379',
            'password' => Hash::make('manager123'),
        ]);

        $role = Role::create(['roleName' => 'manager']);

        EmployeeRole::create([
            'employeeID' => $manager['employeeID'],
            'roleID' => $role['roleID'],
        ]);

        // Attempt to login
        $testResponse = $this->post(route('login'), [
            'phone' => '045618379',
            'password' => 'manager123',
        ]);

        // Assert the manager is redirected to the manager dashboard
        $testResponse->assertRedirect(route('manager.dashboard'));
        $this->assertAuthenticatedAs($manager, 'employee');
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Create a user in the database
        Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04'),
        ]);

        // Attempt to login with wrong password
        $testResponse = $this->post(route('login'), [
            'phone' => '123456789',
            'password' => 'wrongpassword',
        ]);

        // Assert the user is redirected back with errors
        $testResponse->assertSessionHasErrors(['phone']);
        $this->assertGuest('employee');
    }

    public function test_login_validation_rules(): void
    {
        // Attempt to login without providing phone and password
        $testResponse = $this->post(route('login'), []);

        // Assert validation errors for phone and password
        $testResponse->assertSessionHasErrors(['phone', 'password']);
    }

    public function test_login_validation_messages(): void
    {
        // Attempt to login without providing phone and password
        $testResponse = $this->post(route('login'), []);

        // Assert custom validation messages
        $testResponse->assertSessionHasErrors([
            'phone' => 'Fusha e numrit të telefonit është e detyrueshme.',
            'password' => 'Fusha e fjalëkalimit është e detyrueshme.',
        ]);

        $this->assertGuest('employee');
    }

    public function test_logout_functionality(): void
    {
        // Create a test employee
        $employee = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04'),
        ]);

        // Log in the employee
        Auth::guard('employee')->login($employee);

        // Start a session (required for session-related operations like logout)
        $this->withSession([]);

        // Simulate a logout request
        $testResponse = $this->post('/logout', [
            '_token' => csrf_token(),
        ]);

        // Assert that the user is redirected to the login page
        $testResponse->assertRedirect(route('loginPage'));

        // Assert that the success message is present
        $testResponse->assertSessionHas('success', 'You have been logged out.');

        // Assert that the user is logged out
        $this->assertGuest('employee');
    }
}
