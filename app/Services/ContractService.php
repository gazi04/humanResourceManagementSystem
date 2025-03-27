<?php

namespace App\Services;

use App\Models\Contract;
use App\Services\Interfaces\ContractServiceInterface;
use Illuminate\Support\Facades\DB;

class ContractService implements ContractServiceInterface
{
    public function createContract(array $data): Contract
    {

    }

    public function updateContract(Contract $contract, array $data): Contract
    {

    }

    public function getContract(Contract $contract): Contract
    {

    }

    public function getEmployeeContracts(int $employeeID): array
    {

    }
}
