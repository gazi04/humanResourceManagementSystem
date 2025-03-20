<?php

namespace App\Services;

use App\Http\Requests\Employeers\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use App\Services\Interfaces\EmployeeServiceInterface;
use Hash;
use Illuminate\Support\Facades\DB;

class EmployeeService implements EmployeeServiceInterface
{
    public function createEmployee(Role $role, array $data): EmployeeRole
    {
        return DB::transaction(function () use ($role, $data) {
            $employee = Employee::create($data);

            return EmployeeRole::create([
                'employeeID' => $employee->employeeID,
                'roleID' => $role->roleID,
            ]);
        });
    }

    public function updateEmployee(Employee $employee, UpdateEmployeeRequest $request): Employee
    {
        return DB::transaction(function () use ($employee, $request): Employee {
            DB::table('employees')->where('employeeID', $employee->employeeID)
                ->update($request->only(
                    'firstName',
                    'lastName',
                    'email',
                    'phone',
                    'jobTitle',
                    'status',
                    'departmentID',
                ));

            if ($request->has('password')) {
                DB::table('employees')->where('employeeID', $employee->employeeID)
                ->update(['password' => Hash::make($request->password)]);
            }

            return $employee;
        });
    }

    public function deleteEmployee(Employee $employee): void
    {
        DB::transaction(function () use ($employee): void {
            EmployeeRole::where('employeeID', $employee->employeeID)->delete();

            $employee->delete();
        });
    }

    public function assignRole(Employee $employee, Role $role): void
    {
        DB::transaction(function () use ($employee, $role) {
            /** @var EmployeeRole $employeeRole */
            $employeeRole = EmployeeRole::where('employeeID', $employee->employeeID);
            $employeeRole->roleID = $role->roleID;
            $employeeRole->update();
        });
    }
}
