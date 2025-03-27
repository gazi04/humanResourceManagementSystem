<?php

namespace App\Services\Interfaces;

use App\Models\Contract;

interface ContractServiceInterface
{
    public function createContract(array $data): Contract;

    public function updateContract(Contract $contract, array $data): Contract;

    public function getContract(Contract $contract): Contract;

    public function getEmployeeContracts(int $employeeID): array;
}
