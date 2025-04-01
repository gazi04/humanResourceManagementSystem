<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contract\DeleteContractRequest;
use App\Http\Requests\Contract\DownloadContractRequest;
use App\Http\Requests\Contract\GetEmployeeContractsRequest;
use App\Http\Requests\Contract\UpdateContractRequest;
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

        try {
            return $this->contractService->getEmployeeContracts($employee);
        } catch (\Exception $e) {
            Log::error('Gjatë ngarkimit të kontratave ndodhi ky gabim: ', [$e->getMessage()]);

            return back()->with('error', 'Ndodhi një gabim në sistem me marrjen e kontratave, provoni përsëri më vonë.');
        }
    }

    public function create(UploadContractRequest $request)
    {
        $validated = $request->only('employeeID', 'contract_file');
        $employee = Employee::where('employeeID', $validated['employeeID'])->first();

        try {
            $this->contractService->uploadContract($employee, [
                'contractPath' => $request->file('contract_file'),
            ]);

            return back()->with('success', 'Kontrata u ngarkua me sukses.');
        } catch (\Exception $e) {
            Log::error('Gjatë ngarkimit të kontratës ndodhi ky gabim: ', [$e->getMessage()]);

            return back()->with('error', 'Ndodhi një gabim në sistem me ngarkimin e kontratës, provoni përsëri më vonë.');
        }
    }

    public function show(DownloadContractRequest $request)
    {
        $validated = $request->only('contractID');
        $contract = Contract::where('contractID', $validated['contractID'])->first();

        try {
            return $this->contractService->downloadContract($contract);
        } catch (\Exception $e) {
            Log::error('Gjatë shkarkimit të kontratës ndodhi ky gabim: ', [$e->getMessage()]);

            return back()->with('error', 'Ndodhi një gabim në sistem me ngarkimin e kontratës, provoni përsëri më vonë.');
        }
    }

    public function update(UpdateContractRequest $request)
    {
        $validated = $request->only('employeeID', 'contractID', 'contract_file');

        $employee = Employee::where('employeeID', $validated['employeeID'])->first();
        $contract = Contract::where('contractID', $validated['contractID'])->first();

        try {
            $this->contractService->updateContract($contract, $employee, $validated);
            return redirect()->back()->with('success', 'Kontrata u përditësua me sukses.');
        } catch (\Exception $e) {
            Log::error('Gjatë përditësimit të kontratës ndodhi ky gabim: ', [$e->getMessage()]);

            return back()->with('error', 'Ndodhi një gabim në sistem me përditësimit të kontratës, provoni përsëri më vonë.');
        }
    }

    public function delete(DeleteContractRequest $request)
    {
        $validated = $request->only('contractID');
        $contract = Contract::where('contractID', $validated['contractID'])->first();

        try {
            $this->contractService->deleteContract($contract);
            return redirect()->back()->with('success', 'Kontrata u fshi me sukses.');
        } catch (\Exception $e) {
            Log::error('Gjatë fshirjes së kontratës ndodhi ky gabim: ', [$e->getMessage()]);

            return back()->with('error', 'Ndodhi një gabim në sistem me fshirjen e kontratës, provoni përsëri më vonë.');
        }
    }
}
