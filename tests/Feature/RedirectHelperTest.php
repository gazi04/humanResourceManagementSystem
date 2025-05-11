<?php

use App\Models\Employee;
use App\Traits\RedirectHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    // Run migrations before each test
    $this->artisan('migrate:fresh');
});

it('redirects admin to admin dashboard', function (): void {
    // Create an admin employee
    $admin = Employee::factory()->admin()->create();

    // Log in the admin
    Auth::guard('employee')->login($admin);

    // Create a mock request
    $request = Request::create('/');

    // Use the trait
    $redirect = (new class
    {
        use RedirectHelper;
    })->toDashboard($request);

    // Assert the redirect
    expect($redirect->getTargetUrl())->toContain(route('admin.dashboard'));
});

it('redirects hr to hr dashboard', function (): void {
    // Create an HR employee
    $hr = Employee::factory()->hr()->create();

    // Log in the HR
    Auth::guard('employee')->login($hr);

    // Create a mock request
    $request = Request::create('/');

    // Use the trait
    $redirect = (new class
    {
        use RedirectHelper;
    })->toDashboard($request);

    // Assert the redirect
    expect($redirect->getTargetUrl())->toContain(route('hr.dashboard'));
});

it('redirects manager to manager dashboard', function (): void {
    // Create a manager employee
    $manager = Employee::factory()->manager()->create();

    // Log in the manager
    Auth::guard('employee')->login($manager);

    // Create a mock request
    $request = Request::create('/');

    // Use the trait
    $redirect = (new class
    {
        use RedirectHelper;
    })->toDashboard($request);

    // Assert the redirect
    expect($redirect->getTargetUrl())->toContain(route('manager.dashboard'));
});

it('redirects employee to employee dashboard', function (): void {
    // Create a regular employee
    $employee = Employee::factory()->regularEmployee()->create();

    // Log in the employee
    Auth::guard('employee')->login($employee);

    // Create a mock request
    $request = Request::create('/');

    // Use the trait
    $redirect = (new class
    {
        use RedirectHelper;
    })->toDashboard($request);

    // Assert the redirect
    expect($redirect->getTargetUrl())->toContain(route('employee.dashboard'));
});
