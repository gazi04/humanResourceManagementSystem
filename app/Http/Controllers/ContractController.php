<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contract\DownloadContractRequest;
use App\Http\Requests\Contract\GetEmployeeContractsRequest;
use App\Http\Requests\Contract\UploadContractRequest;
use App\Models\Contract;
use App\Models\Employee;
use App\Services\ContractService;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function index(GetEmployeeContractsRequest $request)
    {
        $validated = $request->only('employeeID');
        $employee = Employee::where('employeeID', $validated['employeeID'])->first();

        if (! $employee) {
            return back()->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        try {
            return $this->contractService->getEmployeeContracts($employee);
        } catch (\Exception $e) {
            Log::error('Gjatë ngarkimit të kontratës ndodhi ky gabim: ', $e->getMessage());

            return back()->with('error', 'Ndodhi një gabim në sistem me marrjen e kontratave, provoni përsëri më vonë.');
        }
    }

    public function upload(UploadContractRequest $request)
    {
        $validated = $request->only('employeeID', 'contract_file');
        $employee = Employee::where('employeeID', $validated['employeeID'])->first();

        if (! $employee) {
            return back()->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        try {
            $this->contractService->uploadContract($employee, [
                'contractPath' => $request->file('contract_file'),
            ]);

            return back()->with('success', 'Kontrata u ngarkua me sukses.');
        } catch (\Exception $e) {
            Log::error('Gjatë ngarkimit të kontratës ndodhi ky gabim: ', $e->getMessage());

            return back()->with('error', 'Ndodhi një gabim në sistem me ngarkimin e kontratës, provoni përsëri më vonë.');
        }
    }

    public function download(DownloadContractRequest $request)
    {
        $validated = $request->only('contractID');
        $contract = Contract::where('contractID', $validated['contractID'])->first();

        if (! $contract) {
            return back()->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        try {
            return $this->contractService->downloadContract($contract);
        } catch (\Exception $e) {
            Log::error('Gjatë ngarkimit të kontratës ndodhi ky gabim: ', $e->getMessage());

            return back()->with('error', 'Ndodhi një gabim në sistem me ngarkimin e kontratës, provoni përsëri më vonë.');
        }
    }
}
