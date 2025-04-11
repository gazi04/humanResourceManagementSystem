<?php

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->artisan('migrate');

    $this->employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '987654321',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $this->employee->employeeID,
        'roleID' => 2,
    ]);

    Auth::guard('employee')->login($this->employee);
});

it('successfully creates a ticket with valid data', function (): void {
    $response = $this->post(route('hr.ticket.create'), [
        'subject' => 'Test Subject',
        'description' => 'This is a test ticket description',
    ]);

    $response->assertRedirectToRoute('hr.dashboard')
        ->assertSessionHas('success', 'Bileta u krijua me sukses.');

    $this->assertDatabaseHas('tickets', [
        'employeeID' => $this->employee->employeeID,
        'subject' => 'Test Subject',
        'description' => 'This is a test ticket description',
    ]);
});

it('successfully creates a ticket without subject', function (): void {
    $response = $this->post(route('hr.ticket.create'), [
        'description' => 'Ticket with no subject',
    ]);

    $response->assertRedirectToRoute('hr.dashboard')
        ->assertSessionHas('success', 'Bileta u krijua me sukses.');

    $this->assertDatabaseHas('tickets', [
        'employeeID' => $this->employee->employeeID,
        'subject' => null,
        'description' => 'Ticket with no subject',
    ]);
});

it('successfully creates a ticket as an HR, Manager and Employee', function (): void {
    $manager = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@exaaample.com',
        'phone' => '987904111',
        'password' => Hash::make('password123'),
    ]);
    EmployeeRole::create([
        'employeeID' => $manager->employeeID,
        'roleID' => 4,
    ]);

    $employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@exaalmple.com',
        'phone' => '987904110',
        'password' => Hash::make('password123'),
    ]);
    EmployeeRole::create([
        'employeeID' => $employee->employeeID,
        'roleID' => 3,
    ]);

    Auth::guard('employee')->logout();
    Auth::guard('employee')->login($manager);
    $response = $this->post(route('manager.ticket.create'), [
        'subject' => 'Test Subject from an manager',
        'description' => 'This is a test ticket description',
    ]);
    $response->assertRedirectToRoute('manager.dashboard')
        ->assertSessionHas('success', 'Bileta u krijua me sukses.');

    $this->assertDatabaseHas('tickets', [
        'employeeID' => $manager->employeeID,
        'subject' => 'Test Subject from an manager',
        'description' => 'This is a test ticket description',
    ]);

    Auth::guard('employee')->logout();
    Auth::guard('employee')->login($employee);
    $response = $this->post(route('employee.ticket.create'), [
        'subject' => 'Test Subject from an employee',
        'description' => 'This is a test ticket description',
    ]);
    $response->assertRedirectToRoute('employee.dashboard')
        ->assertSessionHas('success', 'Bileta u krijua me sukses.');
    $this->assertDatabaseHas('tickets', [
        'employeeID' => $employee->employeeID,
        'subject' => 'Test Subject from an employee',
        'description' => 'This is a test ticket description',
    ]);
});

it('fails when description is missing', function (): void {
    $response = $this->post(route('hr.ticket.create'), [
        'subject' => 'Missing description',
    ]);

    $response->assertInvalid(['description' => 'Përshkrimi është i detyrueshëm.']);
});

it('fails when description is not a string', function (): void {
    $response = $this->post(route('hr.ticket.create'), [
        'description' => 12345,
    ]);

    $response->assertInvalid(['description' => 'Përshkrimi duhet të jetë një varg tekstual.']);
});

it('fails when subject exceeds 255 characters', function (): void {
    $response = $this->post(route('hr.ticket.create'), [
        'subject' => str_repeat('a', 256),
        'description' => 'Valid description',
    ]);

    $response->assertInvalid(['subject' => 'Subjekti nuk mund të jetë më i gjatë se 255 karaktere.']);
});

it('handles database query exception errors during ticket creation', function (): void {
    Log::shouldReceive('error')
        ->once();

    $sql = 'insert into `tickets` (`title`, `user_id`) values (?, ?)';
    $bindings = ['Test Ticket', 1];
    $previous = new PDOException('SQLSTATE[23000]: Integrity constraint violation');

    $mock = mock(TicketService::class);
    $mock->shouldReceive('createTicket')
        ->andThrow(new QueryException(env('DB_CONNECTION'),
            $sql, $bindings, $previous
        ));

    $this->app->instance(TicketService::class, $mock);

    $response = $this->post(route('hr.ticket.create'), [
        'subject' => 'Failing ticket',
        'description' => 'This should fail',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('error', 'Procesi i krijimit të biletës ka dështuar provo më vonë përsëri.');
});

it('handles pdo exception errors during ticket creation', function (): void {
    Log::shouldReceive('error')
        ->once();

    $mock = mock(TicketService::class);
    $mock->shouldReceive('createTicket')
        ->andThrow(new \PDOException('Connection failed'));

    $this->app->instance(TicketService::class, $mock);

    $response = $this->post(route('hr.ticket.create'), [
        'subject' => 'Failing ticket',
        'description' => 'This should fail',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('error', 'Procesi i krijimit të biletës ka dështuar provo më vonë përsëri.');
});

it('assigns the authenticated employee ID to the ticket', function (): void {
    $this->post(route('hr.ticket.create'), [
        'description' => 'Test employee assignment',
    ]);

    $ticket = Ticket::first();
    expect($ticket->employeeID)->toBe($this->employee->employeeID);
});

it('requires authentication', function (): void {
    Auth::guard('employee')->logout();

    $response = $this->post(route('hr.ticket.create'), [
        'description' => 'Unauthenticated test',
    ]);

    $response->assertRedirect('/login'); // Or your login route
});

it('requires HR, Manager or Employee role', function (): void {
    $admin = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@exaaample.com',
        'phone' => '987654111',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $admin->employeeID,
        'roleID' => 1,
    ]);

    Auth::guard('employee')->logout();
    Auth::guard('employee')->login($admin);
    $response = $this->post(route('hr.ticket.create'), [
        'description' => 'Non-HR test',
    ]);
    $response->assertForbidden();
    $response = $this->post(route('manager.ticket.create'), [
        'description' => 'Non-HR test',
    ]);
    $response->assertForbidden();
    $response = $this->post(route('employee.ticket.create'), [
        'description' => 'Non-HR test',
    ]);
    $response->assertForbidden();
});
