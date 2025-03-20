<?php

namespace App\Services;

use App\Models\Department;
use App\Services\Interfaces\DepartmentServiceInterface;
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
}
