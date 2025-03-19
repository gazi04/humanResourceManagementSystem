<?php

namespace App\Services\Interfaces;

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;

interface EmployeeServiceInterface
{
    public function createEmployee(Role $role, array $data): EmployeeRole;

    public function updateEmployee(Employee $employee, array $data): Employee;

    public function deleteEmployee(Employee $employee): void;

    public function assignRole(int $employeeID, int $roleID): void;
}
