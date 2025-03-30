<?php

namespace App\Services\Interfaces;

use App\Models\Contract;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ContractServiceInterface
{
    public function uploadContract(Employee $employee, array $data): void;

    public function downloadContract(Contract $contract): StreamedResponse;

    public function getEmployeeContracts(Employee $employee): LengthAwarePaginator;
}
