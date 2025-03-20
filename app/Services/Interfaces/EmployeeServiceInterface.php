<?php

namespace App\Services\Interfaces;

use App\Http\Requests\Employeers\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;

interface EmployeeServiceInterface
{
    public function createEmployee(Role $role, array $data): EmployeeRole;

    public function updateEmployee(Employee $employee, UpdateEmployeeRequest $request): Employee;

    public function deleteEmployee(Employee $employee): void;

    public function assignRole(Employee $employee, Role $role): void;
}
