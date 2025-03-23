<?php

namespace App\Services\Interfaces;

use App\Http\Requests\Employeers\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createEmployee(Role $role, array $data): EmployeeRole;

    public function updateEmployee(Employee $employee, UpdateEmployeeRequest $request): Employee;

    public function deleteEmployee(Employee $employee): void;

    public function assignRole(Employee $employee, Role $role): void;

    public function selectEmployeesBasedOnRoles(int $roleID): LengthAwarePaginator;

    public function getEmployees(): LengthAwarePaginator;
}
