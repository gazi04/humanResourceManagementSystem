<?php

namespace App\Services\Interfaces;

use App\Models\Employee;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ContractServiceInterface
{
    public function uploadContract(Employee $employee, array $data): void;

    public function downloadContract(Employee $employee): StreamedResponse;
}
