<?php

use App\Models\Employee;
use App\Models\EmployeeRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');

    $this->hrUser = Employee::create([
        'firstName' => 'HR',
        'lastName' => 'User',
        'email' => 'hr@example.com',
        'phone' => '123456789',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $this->hrUser->employeeID,
        'roleID' => 2,
    ]);

    Auth::guard('employee')->login($this->hrUser);
});

it('gets the id of the logged in user', function () {
    // Create a class that uses the trait for testing
    $testClass = new class
    {
        use \App\Traits\AuthHelper;
    };

    // Act
    $loggedUserId = $testClass->getLoggedUserID();

    // Assert
    expect($loggedUserId)->toBe($this->hrUser->employeeID);
});

it('throws exception when no user is logged in', function () {
    // Ensure no user is logged in
    Auth::guard('employee')->logout();

    // Create a class that uses the trait for testing
    $testClass = new class
    {
        use \App\Traits\AuthHelper;
    };

    // Assert that it throws an exception
    expect(fn () => $testClass->getLoggedUserID())->toThrow(Exception::class);
});
