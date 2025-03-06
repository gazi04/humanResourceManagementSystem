<?php

namespace Tests\Feature;

use App\Models\Employee;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $user = Employee::create([
            'FirstName' => 'gazi',
            'LastName' => 'halili',
            'Email' => 'gaz@gmail.com',
            'Phone' => '045618376',
            'Password' => bcrypt('gazi04')
        ]);

        // Attempt to login
        $response = $this->post(route('login'), [
            'phone' => '045618376',
            'password' => 'gazi04',
        ]);

        // Assert the user is redirected to the intended page
        $response->assertRedirect(route('dashboard')); // Replace with your intended route
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // Create a user in the database
        $user = Employee::create([
            'FirstName' => 'gazi',
            'LastName' => 'halili',
            'Email' => 'gaz@gmail.com',
            'Phone' => '045618376',
            'Password' => bcrypt('gazi04')
        ]);

        // Attempt to login with wrong password
        $response = $this->post(route('login'), [
            'phone' => '123456789',
            'password' => 'wrongpassword',
        ]);

        // Assert the user is redirected back with errors
        $response->assertSessionHasErrors(['password']);
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
    }

    public function test_signup_page_is_accessible()
    {
        $respone = $this->get(route('signupPage'));

        $respone->assertStatus(200);
        $respone->assertViewIs('Auth.SignUp');
    }

    public function test_user_can_register()
    {
        $reponse = $this->post(route('register'));
    }
}
