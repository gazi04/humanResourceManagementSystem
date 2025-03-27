<?php

namespace App\Http\Controllers;

use App\Http\Requests\File\DownloadFileRequest;
use App\Http\Requests\File\UploadFileRequest;
use App\Services\ContractService;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function upload(UploadFileRequest $request)
    {
        try {
            $this->contractService->uploadContract($employee, [
                'contract_file' => $request->file('contract'),
            ]);

            return back()->with('success', 'Contract uploaded successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function download(DownloadFileRequest $request): void
    {
        $request->only('contract');
    }
}
