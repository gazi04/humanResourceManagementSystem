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

    Storage::fake('contracts');

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

it('requires authentication to perform actions on contracts', function (): void {
    Auth::guard('employee')->logout();

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);
    $response->assertRedirect(route('loginPage'));

    $response = $this->post(route('hr.employee.contract.download'), [
        'employeeID' => $this->employee->employeeID,
    ]);
    $response->assertRedirect(route('loginPage'));

    $response = $this->patch(route('hr.employee.contract.update'), ['employeeID' => 34, 'contractID' => 89]);
    $response->assertRedirect(route('loginPage'));

    $response = $this->delete(route('hr.employee.contract.delete'), ['contractID' => 9]);
    $response->assertRedirect(route('loginPage'));
});

it('uploads contract with invalid data', function (): void {
    $response = $this->post(route('hr.employee.contract.upload'), []);
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit është e detyrueshme.',
        'contract_file' => 'Kontrata është e detyrueshme.',
    ]);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.txt', 1000),
    ]);
    $response->assertSessionHasErrors(['contract_file' => 'Kontrata duhet të jetë një skedar PDF.']);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 3000),
    ]);
    $response->assertSessionHasErrors(['contract_file' => 'Kontrata nuk mund të jetë më e madhe se 2MB.']);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => 'asdf',
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);
    $response->assertSessionHasErrors(['employeeID' => 'ID e punonjësit duhet të jetë një numër i plotë.']);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => 0,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);
    $response->assertSessionHasErrors(['employeeID' => 'ID e punonjësit duhet të jetë më e madhe se 0.']);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => 9999,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);
    $response->assertSessionHasErrors(['employeeID' => 'Punonjësi me këtë ID nuk egziston.']);
});

it('uploads contract with valid data and look for the contract in the view', function (): void {
    $file = UploadedFile::fake()->create('contract.pdf', 1000);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => $file,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Kontrata u ngarkua me sukses.');

    $contract = Contract::first();
    expect($contract)->not->toBeNull()
        ->and($contract->employeeID)->toBe($this->employee->employeeID)
        ->and($contract->contractPath)->toMatch('/^contract_\d+\.pdf$/');

    Storage::disk('contracts')->assertExists($contract->contractPath);

    $response = $this->post(route('hr.employee.profile'), [
        'employeeID' => $this->employee->employeeID,
    ]);
    $response->assertSee($contract->contractPath);
});

it('handles upload errors gracefully', function (): void {
    // Mock a storage failure
    Storage::shouldReceive('disk->putFileAs')
        ->once()
        ->andReturn(false);

    $response = $this->post(route('hr.employee.contract.upload'), [
        'employeeID' => $this->employee->employeeID,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 1000),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});

it('downloads contract with invalid data', function (): void {
    $response = $this->post(route('hr.employee.contract.download'), []);
    $response->assertSessionHasErrors(['contractID' => 'ID e kontratës është e detyrueshme.']);

    $response = $this->post(route('hr.employee.contract.download'), [
        'contractID' => 'asdf',
    ]);
    $response->assertSessionHasErrors(['contractID' => 'ID e kontratës duhet të jetë një numër i plotë.']);

    $response = $this->post(route('hr.employee.contract.download'), [
        'contractID' => 0,
    ]);
    $response->assertSessionHasErrors(['contractID' => 'ID e kontratës duhet të jetë më e madhe se 0.']);

    $response = $this->post(route('hr.employee.contract.download'), [
        'contractID' => 9999,
    ]);
    $response->assertSessionHasErrors(['contractID' => 'Kontrata me këtë ID nuk egziston.']);
});

it('downloads contract successfully', function (): void {
    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'test_contract.pdf',
    ]);

    Storage::disk('contracts')->put('test_contract.pdf', 'test content');

    $response = $this->post(route('hr.employee.contract.download'), [
        'contractID' => $contract->contractID,
    ]);

    expect($response->baseResponse)->toBeInstanceOf(StreamedResponse::class);
    expect($response->headers->get('Content-Disposition'))
        ->toContain('attachment; filename=test_contract.pdf');
});

it('handles download errors when file missing', function (): void {
    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'missing_contract.pdf',
    ]);
    $response = $this->post(route('hr.employee.contract.download'), [
        'contractID' => $contract->contractID,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Skedari i kontratës nuk gjendet në sistem.');
});

it('updates contract with invalid data', function (): void {
    $response = $this->patch(route('hr.employee.contract.update'), []);
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit është e detyrueshme.',
        'contractID' => 'ID e kontratës është e detyrueshme.',
        'contract_file' => 'Kontrata është e detyrueshme.',
    ]);

    $response = $this->patch(route('hr.employee.contract.update'), [
        'employeeID' => 'tesr',
        'contractID' => 'test',
        'contract_file' => 'test',
    ]);
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit duhet të jetë një numër i plotë.',
        'contractID' => 'ID e kontratës duhet të jetë një numër i plotë.',
        'contract_file' => 'Kontrata duhet të jetë një skedar.',
    ]);

    $response = $this->patch(route('hr.employee.contract.update'), [
        'employeeID' => 0,
        'contractID' => 0,
        'contract_file' => UploadedFile::fake()->create('new_contract.txt', 1000),
    ]);
    $response->assertSessionHasErrors([
        'employeeID' => 'ID e punonjësit duhet të jetë më e madhe se 0.',
        'contractID' => 'ID e kontratës duhet të jetë më e madhe se 0.',
        'contract_file' => 'Kontrata duhet të jetë një skedar PDF.',
    ]);

    $response = $this->patch(route('hr.employee.contract.update'), [
        'employeeID' => 999,
        'contractID' => 999,
        'contract_file' => UploadedFile::fake()->create('contract.pdf', 3000),
    ]);
    $response->assertSessionHasErrors([
        'employeeID' => 'Punonjësi me këtë ID nuk egziston.',
        'contractID' => 'Kontrata me këtë ID nuk egziston.',
        'contract_file' => 'Kontrata nuk mund të jetë më e madhe se 2MB.',
    ]);
});

it('update contract with valid data', function (): void {
    Storage::disk('contracts')->put('old_contract.pdf', 'dummy content');
    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'old_contract.pdf',
    ]);

    $newFile = UploadedFile::fake()->create('new_contract.pdf', 1024, 'application/pdf');

    $response = $this->patch(route('hr.employee.contract.update'), [
        'employeeID' => $this->employee->employeeID,
        'contractID' => $contract->contractID,
        'contract_file' => $newFile,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success', 'Kontrata u përditësua me sukses.');

    Storage::disk('contracts')->assertMissing('old_contract.pdf');

    $contract->refresh();
    Storage::disk('contracts')->assertExists($contract->contractPath);

    $this->assertDatabaseHas('contracts', [
        'contractID' => $contract->contractID,
        'employeeID' => $this->employee->employeeID,
        'contractPath' => $contract->contractPath,
    ]);

    expect($contract->contractPath)
        ->not->toBe('old_contract.pdf')
        ->and($contract->contractPath)
        ->toMatch('/^contract_\d+\.pdf$/');
});

it('deletes contract with invalid data', function (): void {
    $response = $this->delete(route('hr.employee.contract.delete'), []);
    $response->assertSessionHasErrors(['contractID' => 'ID e kontratës është e detyrueshme.']);

    $response = $this->delete(route('hr.employee.contract.delete'), [
        'contractID' => 'asdf',
    ]);
    $response->assertSessionHasErrors(['contractID' => 'ID e kontratës duhet të jetë një numër i plotë.']);

    $response = $this->delete(route('hr.employee.contract.delete'), [
        'contractID' => 0,
    ]);
    $response->assertSessionHasErrors(['contractID' => 'ID e kontratës duhet të jetë më e madhe se 0.']);

    $response = $this->delete(route('hr.employee.contract.delete'), [
        'contractID' => 9999,
    ]);
    $response->assertSessionHasErrors(['contractID' => 'Kontrata me këtë ID nuk egziston.']);
});

it('deletes contract with valid data', function (): void {
    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'contract_to_delete.pdf',
    ]);

    $response = $this->delete(route('hr.employee.contract.delete'), [
        'contractID' => $contract->contractID,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success', 'Kontrata u fshi me sukses.');

    Storage::disk('contracts')->assertMissing('contract_to_delete.pdf');

    $this->assertDatabaseMissing('contracts', [
        'contractID' => $contract->contractID,
    ]);
});

it('lists employee contracts paginated', function (): void {
    for ($i = 0; $i < 15; $i++) {
        Contract::create([
            'employeeID' => $this->employee->employeeID,
            'contractPath' => 'testing_contract.pdf',
        ]);
    }

    $response = $this->post(route('hr.employee.contract.show', [
        'employeeID' => $this->employee->employeeID,
    ]));

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(5)
        ->and($response->json('total'))->toBe(15);
});

it('updates contract successfully', function (): void {
    $newFile = UploadedFile::fake()->create('new_contract.pdf', 1000);

    $contract = Contract::create([
        'employeeID' => $this->employee->employeeID,
        'contractPath' => 'existing_contract.pdf',
    ]);

    Storage::disk('contracts')->put('existing_contract.pdf', 'test content');
    $response = $this->patch(route('hr.employee.contract.update'), [
        'employeeID' => $this->employee->employeeID,
        'contractID' => $contract->contractID,
        'contract_file' => $newFile,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    Storage::disk('contracts')->assertMissing('existing_contract.pdf');

    $contract->refresh();
    Storage::disk('contracts')->assertExists($contract->contractPath);
});
