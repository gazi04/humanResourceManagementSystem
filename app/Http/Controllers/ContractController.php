<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\DownloadFileRequest;
use App\Http\Requests\File\UploadFileRequest;
use App\Models\Employee;
use App\Services\ContractService;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function upload(UploadFileRequest $request)
    {
        $validated = $request->only('employeeID', 'contract_file');
        $employee = Employee::where('employeeID', $validated['employeeID'])->first();

        if (!$employee) {
            return redirect()->route('hr.dashboard')->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        try {
            $this->contractService->uploadContract($employee, [
                'contract_path' => $request->file('contract_file'),
            ]);

            return back()->with('success', 'Kontrata u ngarkua me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function download(DownloadFileRequest $request)
    {
        $validated = $request->only('employeeID');
        $employee = Employee::where('employeeID', $validated['employeeID'])->first();

        if (!$employee) {
            return redirect()->route('hr.dashboard')->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        try {
            $this->contractService->downloadContract($employee);
            return back()->with('success', 'Kontrata u ngarkua me sukses.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
