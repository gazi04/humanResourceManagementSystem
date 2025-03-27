<?php

namespace App\Services\Interfaces;

use App\Models\Contract;
use Illuminate\Pagination\LengthAwarePaginator;

interface ContractServiceInterface
{
    public function createContract(array $data): Contract;

    public function getContract(int $contractID): Contract;

    public function getEmployeeContracts(int $employeeID): LengthAwarePaginator;
}
