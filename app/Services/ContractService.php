<?php

namespace App\Services;

use App\Models\Contract;
use App\Services\Interfaces\ContractServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ContractService implements ContractServiceInterface
{
    public function createContract(array $data): Contract
    {
        return DB::transaction(function () use ($data): Contract {
            return Contract::create($data);
        });
    }

    public function getContract(int $contractID): Contract
    {
        return DB::transaction(function () use ($contractID): Contract {
            return Contract::find($contractID);
        });
    }

    public function getEmployeeContracts(int $employeeID): LengthAwarePaginator
    {
        return DB::transaction(function () use ($employeeID): LengthAwarePaginator {
            return DB::table('contracts')
                ->where('employeeID', $employeeID)
                ->select([
                    'contractID',
                    'employeeID',
                    'filePath',
                    'uploadDate'
                ])
                ->paginate(10);
        });
    }
}
