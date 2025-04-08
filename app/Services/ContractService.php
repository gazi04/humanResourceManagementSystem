<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Employee;
use App\Services\Interfaces\ContractServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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

        throw_if($path === false, new \RuntimeException('Dështoi në ruajtjen e skedarit të kontratës.'));

        Contract::create([
            'employeeID' => $employee->employeeID,
            'contractPath' => $path,
        ]);
    }

    public function downloadContract(Contract $contract): StreamedResponse
    {
        throw_unless(Storage::disk('contracts')->exists($contract->contractPath), new \Exception('Skedari i kontratës nuk gjendet në sistem.'));

        return Storage::disk('contracts')->download($contract->contractPath);
    }

    public function updateContract(Contract $contract, Employee $employee, array $data): void
    {
        DB::transaction(function () use ($contract, $employee, $data) {
            throw_unless($contract->employeeID === $employee->employeeID, new \InvalidArgumentException('The specified contract does not belong to this employee.'));

            $file = $data['contract_file'] ?? null;

            throw_unless($file instanceof UploadedFile, new \InvalidArgumentException('Invalid file upload'));

            $filename = 'contract_'.time().'.'.$file->getClientOriginalExtension();

            $newPath = Storage::disk('contracts')->putFileAs(
                '',
                $file,
                $filename
            );

            throw_if($newPath === false, new \RuntimeException('Failed to store the new contract file.'));

            if (Storage::disk('contracts')->exists($contract->contractPath)) {
                Storage::disk('contracts')->delete($contract->contractPath);
            }

            $contract->update([
                'contractPath' => $newPath,
            ]);
        });
    }

    public function deleteContract(Contract $contract): void
    {
        DB::transaction(function () use ($contract) {
            if (Storage::disk('contracts')->exists($contract->contractPath)) {
                Storage::disk('contracts')->delete($contract->contractPath);
            }

            $contract->delete();
        });
    }

    public function getEmployeeContracts(int $employeeID): LengthAwarePaginator
    {
        return DB::transaction(fn (): LengthAwarePaginator => DB::table('contracts')->where('employeeID', $employeeID)
            ->select('contractID', 'contractPath', 'created_at')
            ->latest('created_at')
            ->paginate(5));
    }
}
