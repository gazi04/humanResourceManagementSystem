<?php

namespace App\Services;

use App\Models\Department;
use App\Services\Interfaces\DepartmentServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DepartmentService implements DepartmentServiceInterface
{
    public function createDepartment(array $data): Department
    {
        return DB::transaction(fn () => Department::create($data));
    }

    public function updateDepartment(Department $department, array $data): Department
    {
        return DB::transaction(function () use ($department, $data): Department {
            $department->update($data);

            return $department;
        });
    }

    public function deleteDepartment(Department $department): void
    {
        DB::transaction(function () use ($department): void {
            $department->delete();
        });
    }

    public function showDepartments(): LengthAwarePaginator
    {
        return DB::transaction(fn() => DB::table('departments')
            ->leftJoin('employees', 'departments.supervisorID', '=', 'employees.employeeID')
            ->select(
                'departments.departmentID',
                'departments.departmentName',
                'employees.firstName as supervisor_firstName',
                'employees.lastName as supervisor_lastName'
            )
            ->paginate(10));
    }

    public function assignManager(Department $department, int $managerID): void
    {
        DB::transaction(function () use ($department, $managerID): void {
            $department->update(['supervisorID' => $managerID]);
        });
    }
}
