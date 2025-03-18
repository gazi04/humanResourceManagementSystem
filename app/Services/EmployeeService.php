<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use App\Services\Interfaces\EmployeeServiceInterface;
use Illuminate\Support\Facades\DB;

class EmployeeService implements EmployeeServiceInterface
{
    public function createEmployee(array $data): Employee
    {
        return DB::transaction(fn () => Employee::create($data));
    }

    public function updateEmployee(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data): Employee {
            $employee->update($data);

            return $employee;
        });
    }

    public function deleteEmployee(Employee $employee): void
    {
        DB::transaction(function () use ($employee): void {
            $employee->delete();
        });
    }

    public function assignRole(int $employeeID, int $roleID): void
    {
        DB::transaction(function () use ($employeeID, $roleID) {
            $employee = Employee::find($employeeID);
            $role = Role::find($roleID);

            throw_unless($employee, new \RuntimeException('Punonjësi nuk u gjet në bazën e të dhënave.'));

            throw_unless($role, new \RuntimeException('Roli nuk u gjet në bazën e të dhënave.'));

            $employeeRole = EmployeeRole::where('employeeID', $employeeID)
                ->first();

            if ($employeeRole) {
                return $employeeRole::update([
                    'roleID' => $roleID,
                ]);
            }

            return EmployeeRole::create([
                'employeeID' => $employeeID,
                'roleID' => $roleID,
            ]);
        });
    }
}
