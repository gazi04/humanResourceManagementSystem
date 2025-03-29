<?php
use App\Models\Employee;
use App\Models\EmployeeRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses()->group('contracts');

beforeEach(function () {
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

it('requires authentication for upload', function () {
    Auth::guard('employee')->logout();

    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);

    $response->assertRedirect(route('loginPage'));
});

it('validates upload request', function () {
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

it('uploads contract successfully', function () {
    $file = UploadedFile::fake()->create('contract.pdf', 1000);

    $response = $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Kontrata u ngarkua me sukses.');

    // Assert file was stored
    $this->employee->refresh();
    Storage::disk('contracts')->assertExists($this->employee->contractPath);

    // Assert filename follows expected pattern
    expect($this->employee->contractPath)->toMatch('/^contract_\d+\.pdf$/');
});

it('handles upload errors gracefully', function () {
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

it('requires authentication for download', function () {
    Auth::guard('employee')->logout();

    $response = $this->post(route('hr.download-contract'), [
        'employeeID' => $this->employee->employeeID,
    ]);

    $response->assertRedirect(route('loginPage'));
});

it('validates download request', function () {
    // Test missing employeeID
    $response = $this->post(route('hr.download-contract'), []);
    $response->assertSessionHasErrors(['employeeID']);

    // Test invalid employee
    $response = $this->post(route('hr.download-contract'), [
        'employeeID' => 9999,
    ]);
    $response->assertSessionHasErrors(['employeeID']);
});

it('downloads contract successfully', function () {
    // First upload a contract
    $file = UploadedFile::fake()->create('contract.pdf', 1000);
    $this->post(route('hr.upload-contract'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => $file,
    ]);

    $this->employee->refresh();
    $response = $this->post(route('hr.download-contract'), [
        'employeeID' => $this->employee->employeeID,
    ]);

    // Assert download response
    expect($response->baseResponse)->toBeInstanceOf(StreamedResponse::class);

    expect($response->headers->get('Content-Disposition'))
        ->toBe('attachment; filename='.$this->employee->contractPath);
});

it('handles download errors when no contract exists', function () {
    $response = $this->post(route('hr.download-contract'), [
        'employeeID' => $this->employee->employeeID,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Nuk u gjet asnjë kontratë për këtë punonjës.');
});

it('handles download errors when file missing', function () {
    // Set contract path but don't actually store the file
    $this->employee->update(['contractPath' => 'missing_contract.pdf']);

    $response = $this->post(route('hr.download-contract'), [
        'employeeID' => $this->employee->employeeID,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Skedari i kontratës nuk gjendet në sistem.');
});
