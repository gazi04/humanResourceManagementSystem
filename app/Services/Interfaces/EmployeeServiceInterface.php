<?php

namespace App\Services\Interfaces;

use App\Models\Employee;

interface EmployeeServiceInterface
{
    public function createEmployee(array $data): Employee;

    public function updateEmployee(Employee $employee, array $data): Employee;

    public function deleteEmployee(Employee $employee): void;

    public function assignRole(int $employeeID, int $roleID): void;
}
