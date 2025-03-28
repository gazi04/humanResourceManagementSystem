<?php

namespace App\Services;

use App\Models\Employee;
use App\Services\Interfaces\ContractServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractService implements ContractServiceInterface
{
    public function uploadContract(Employee $employee, array $data): void
    {
        $file = $data['contract_path'];

        throw_unless($file instanceof UploadedFile, new \InvalidArgumentException('Ngarkimi i skedarit i pavlefshëm.'));

        $filename = 'contract_' . time() . '.' . $file->getClientOriginalExtension();

        $path = Storage::disk('contracts')->putFileAs(
            '',
            $file,
            $filename
        );

        $employee->update([
            'contract_path' => $path,
        ]);
    }

    public function downloadContract(Employee $employee): StreamedResponse
    {
        throw_unless($employee->contract_path, new \Exception('No contract found for this employee'));

        $fullPath = Storage::disk('contracts')->path($employee->contract_path);

        Log::debug('Attempting to download contract', [
            'employee_id' => $employee->employeeID,
            'contract_path' => $employee->contract_path,
            'full_storage_path' => $fullPath,
            'file_exists' => file_exists($fullPath),
            'storage_exists' => Storage::disk('contracts')->exists($employee->contract_path)
        ]);

        throw_unless(Storage::disk('contracts')->exists($employee->contract_path), new \Exception('Contract file not found in storage'));

        return Storage::disk('contracts')->download(
            $employee->contract_path,
            'contract_' . Str::slug($employee->firstName . '_' . $employee->lastName) . '.pdf'
        );
    }
}
