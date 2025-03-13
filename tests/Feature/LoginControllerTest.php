<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database after each test

    public function test_login_page_is_accessible()
    {
        $response = $this->get(route('loginPage'));

        $response->assertStatus(200);
        $response->assertViewIs('Auth.Login');
    }

    public function test_user_can_login_with_valid_credentials()
    {
        // Create a user in the database
        $employee = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04')
        ]);

        // Attempt to login
        $response = $this->post(route('login'), [
            'phone' => '045618376',
            'password' => 'gazi04',
        ]);

        // Assert the user is redirected to the intended page
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($employee, 'employee');
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // Create a user in the database
        $employee = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04')
        ]);

        // Attempt to login with wrong password
        $response = $this->post(route('login'), [
            'phone' => '123456789',
            'password' => 'wrongpassword',
        ]);

        // Assert the user is redirected back with errors
        $response->assertSessionHasErrors(['phone']);
        $this->assertGuest('employee');
    }

    public function test_login_validation_rules()
    {
        // Attempt to login without providing phone and password
        $response = $this->post(route('login'), []);

        // Assert validation errors for phone and password
        $response->assertSessionHasErrors(['phone', 'password']);
    }

    public function test_login_validation_messages()
    {
        // Attempt to login without providing phone and password
        $response = $this->post(route('login'), []);

        // Assert custom validation messages
        $response->assertSessionHasErrors([
            'phone' => 'Fusha e numrit të telefonit është e detyrueshme.',
            'password' => 'Fusha e fjalëkalimit është e detyrueshme.',
        ]);

        $this->assertGuest('employee');
    }

    public function test_logout_functionality()
    {
        // Create a test employee
        $employee = Employee::create([
            'firstName' => 'gazi',
            'lastName' => 'halili',
            'email' => 'gaz@gmail.com',
            'phone' => '045618376',
            'password' => Hash::make('gazi04')
        ]);

        // Log in the employee
        Auth::guard('employee')->login($employee);

        // Start a session (required for session-related operations like logout)
        $this->withSession([]);

        // Simulate a logout request
        $response = $this->post('/logout', [
            '_token' => csrf_token(),
        ]);

        // Assert that the user is redirected to the login page
        $response->assertRedirect(route('loginPage'));

        // Assert that the success message is present
        $response->assertSessionHas('success', 'You have been logged out.');

        // Assert that the user is logged out
        $this->assertGuest('employee');
    }
}
