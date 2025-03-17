<?php

namespace App\Services\Interfaces;

use App\Models\Department;

interface DepartmentServiceInterface
{
    public function createDepartment(array $data): Department;
    public function updateDepartment(Department $department, array $data): Department;
    public function deleteDepartment(Department $department): void;
}
