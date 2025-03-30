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
        throw_unless($contract->contractPath, new \Exception('Nuk u gjet asnjë kontratë për këtë punonjës.'));

        throw_unless(Storage::disk('contracts')->exists($contract->contractPath), new \Exception('Skedari i kontratës nuk gjendet në sistem.'));

        return Storage::disk('contracts')->download($contract->contractPath);
    }

    public function getEmployeeContracts(Employee $employee): LengthAwarePaginator
    {
        return DB::transaction(fn(): LengthAwarePaginator => DB::table('contracts')->where('employeeID', $employee->employeeID)
            ->select('contractID', 'contractPath')
            ->latest()
            ->paginate(10));
    }
}
