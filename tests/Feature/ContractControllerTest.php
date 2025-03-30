<?php

use App\Models\Contract;
use App\Models\Employee;
use App\Models\EmployeeRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(RefreshDatabase::class);
uses()->group('contracts');

beforeEach(function (): void {
    $this->artisan('migrate');

    // Setup storage fake for contracts
    Storage::fake('contracts');

    // Create HR user and login
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

    // Assign HR role (assuming you have role setup)
    // This would depend on your role implementation
    Auth::guard('employee')->login($this->hrUser);

    // Create test employee
    $this->employee = Employee::create([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '987654321',
        'password' => Hash::make('password123'),
    ]);

    EmployeeRole::create([
        'employeeID' => $this->employee->employeeID,
        'roleID' => 3,
    ]);
});

it('requires authentication for upload', function (): void {
    Auth::guard('employee')->logout();

    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);

    $response->assertRedirect(route('loginPage'));
});

it('validates upload request', function (): void {
    // Test missing fields
    $response = $this->post(route('hr.upload-contract'), []);
    $response->assertSessionHasErrors(['employeeID', 'contract_file']);

    // Test invalid file type
    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.txt', 1000),
    ]);
    $response->assertSessionHasErrors(['contract_file']);

    // Test file size
    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 3000),
    ]);
    $response->assertSessionHasErrors(['contract_file']);

    // Test invalid employee
    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => 9999,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);
    $response->assertSessionHasErrors(['employeeID']);
});

it('uploads contract successfully', function (): void {
    $file = UploadedFile::fake()->create('contract.pdf', 1000);

    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Kontrata u ngarkua me sukses.');

    // Verify contract record was created
    $contract = Contract::first();
    expect($contract)->not->toBeNull()
        ->and($contract->employeeID)->toBe($this->employee->employeeID)
        ->and($contract->contractPath)->toMatch('/^contract_\d+\.pdf$/');

    // Verify file was stored
    Storage::disk('contracts')->assertExists($contract->contractPath);
});

it('handles upload errors gracefully', function (): void {
    // Mock a storage failure
    Storage::shouldReceive('disk->putFileAs')
        ->once()
        ->andReturn(false);

    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('requires authentication for download', function (): void {
    Auth::guard('employee')->logout();

    $response = $this->post(route('hr.download-contract'), [
        'employeeID' => $this->employee->employeeID,
    ]);

    $response->assertRedirect(route('loginPage'));
});

it('validates download request', function (): void {
    // Test missing employeeID
    $response = $this->post(route('hr.download-contract'), []);
    $response->assertSessionHasErrors(['contractID']);

    // Test invalid employee
    $response = $this->post(route('hr.download-contract'), [
        'contractID' => 9999,
    ]);
    $response->assertSessionHasErrors(['contractID']);
});

it('downloads contract successfully', function (): void {
    // Create a contract record
    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'test_contract.pdf',
    ]);

    // Create a fake file in storage
    Storage::disk('contracts')->put('test_contract.pdf', 'test content');

    $response = $this->post(route('hr.download-contract'), [
        'contractID' => $contract->contractID,
    ]);

    expect($response->baseResponse)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('Content-Disposition'))
        ->toContain('attachment; filename=test_contract.pdf');
});

it('handles download errors when no contract exists', function (): void {
    // Non-existent contract
    $response = $this->post(route('hr.download-contract'), [
        'contractID' => 9999,
    ]);
    $response->assertRedirect();
    $response->assertSessionHasErrors([
        'contractID' => 'Kontrata me këtë ID nuk egziston.'
    ]);
});

it('handles download errors when file missing', function (): void {
    // Set contract path but don't actually store the file
    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'missing_contract.pdf',
    ]);
    $response = $this->post(route('hr.download-contract'), [
        'contractID' => $contract->contractID,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Ndodhi një gabim në sistem me ngarkimin e kontratës, provoni përsëri më vonë.');
});

it('lists employee contracts paginated', function () {
    for ($i=0; $i < 15; $i++) {
        Contract::create([
            'employeeID' => $this->employee->employeeID,
            'contractPath' => 'missing_contract.pdf',
        ]);
    }

    $response = $this->post(route('hr.get-contracts', [
        'employeeID' => $this->employee->employeeID,
    ]));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(10)
        ->and($response->json('total'))->toBe(15);
});
