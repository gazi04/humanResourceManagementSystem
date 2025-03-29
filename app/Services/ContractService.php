<?php

namespace App\Services;

use App\Models\Employee;
use App\Services\Interfaces\ContractServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractService implements ContractServiceInterface
{
    public function uploadContract(Employee $employee, array $data): void
    {
        $file = $data['contractPath'];

        throw_unless($file instanceof UploadedFile, new \InvalidArgumentException('Ngarkimi i skedarit i pavlefshëm.'));

        $filename = 'contract_'.time().'.'.$file->getClientOriginalExtension();

        $path = Storage::disk('contracts')->putFileAs(
            '',
            $file,
            $filename
        );

        throw_if($path === false, new \RuntimeException('Failed to store the contract file.'));

        $employee->update([
            'contractPath' => $path,
        ]);
    }

    public function downloadContract(Employee $employee): StreamedResponse
    {
        throw_unless($employee->contractPath, new \Exception('Nuk u gjet asnjë kontratë për këtë punonjës.'));

        throw_unless(Storage::disk('contracts')->exists($employee->contractPath), new \Exception('Skedari i kontratës nuk gjendet në sistem.'));

        return Storage::disk('contracts')->download($employee->contractPath);
    }
}
