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
    /**
     * @param  array<string, mixed>  $data
     */
    public function createEmployee(Role $role, array $data): EmployeeRole
    {
        return DB::transaction(function () use ($role, $data): EmployeeRole {
            /** @var Employee $employee */
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
        DB::transaction(function () use ($employee, $role): void {
            DB::table('employee_roles')->where('employeeID', $employee->employeeID)
                ->update(['roleID' => $role->roleID]);
        });
    }

    public function selectEmployeesBasedOnRoles(int $roleID)
    {
        return DB::transaction(fn () => DB::table('employees as e')
            ->join('employee_roles as er', 'e.employeeID', '=', 'er.employeeID')
            ->join('roles as r', 'er.roleID', '=', 'r.roleID')
            ->leftJoin('departments as d', 'e.departmentID', '=', 'd.departmentID')
            ->leftJoin('employees as s', 'e.supervisorID', '=', 's.employeeID')
            ->where('r.roleID', $roleID)
            ->select([
                'e.employeeID',
                'e.firstName',
                'e.lastName',
                'e.email',
                'e.phone',
                'e.hireDate',
                'e.jobTitle',
                'e.salary',
                'e.status',
                'd.departmentName',
                's.firstName as supervisorFirstName',
                's.lastName as supervisorLastName',
                'r.roleName',
            ])
            ->paginate(10));
    }
}
